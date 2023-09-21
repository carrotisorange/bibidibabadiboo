<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Admin\Controller;

use Zend\Log\Logger;
use Zend\Session\Container;
use Interop\Container\ContainerInterface;
use Zend\View\Helper\HeadTitle;

use Base\Controller\BaseController;
use Base\Service\FormService;
use Base\Service\FormCodeGroupService;
use Base\Service\FormCodeListService;
use Base\Service\FormCodeListGroupMapService;
use Base\Service\FormCodeListPairMapService;
use Base\Service\FormCodePairService;

class AssignFormCodeValuesController extends BaseController
{
    /**
     * @var Array
     */
    private $config;
    
    /**
     * @var Zend\Log\Logger
     */
    private $logger;

    /**
     * @var Base\Service\FormService
     */
    private $serviceForm;
    
    /**
     * @var Zend\Session\Container
     */
    private $session;

    /**
     * @var Base\Service\FormCodeGroupService
     */
     private $serviceFormCodeGroup;
     
    /**
     * @var Base\Service\FormCodeListService
     */
     private $serviceFormCodeList;
     
    /**
     * @var Base\Service\FormCodeListGroupMapService
     */
     private $serviceFormCodeListGroupMap;
     
    /**
     * @var Base\Service\FormCodeListPairMapService
     */
     private $serviceFormCodeListPairMap;

    /**
     * @var Base\Service\FormCodePairService
     */
     private $serviceFormCodePair;

    /**
     * @var Zend\View\Helper\HeadTitle
     */
    protected $helperHeadTitle;

    /**
     * Constructor will be invoked from the ViewKeyedImageControllerFactory
     * @param array  $config        Application configuration
     * @param object $logger        Zend\Log\Logger;
     * @param object $serviceUser   Admin\Form\ViewKeyedImageForm;
     * @param object $serviceService   Base\Service\FormService;
     * @param object $serviceAgency   Base\Service\AgencyService;
     * @param object $serviceFormFieldAttribute   Base\Service\FormFieldAttributeService;
     * @param object $container   Interop\Container\ContainerInterface;
     * @param object $serviceFormNote   Base\Service\FormnoteService;
     * @param object $fieldContainer   Data\Form\ReportForm\FieldContainer;
     * @param object $formHandler   Data\Form\ReportForm\Form;
     */
    public function __construct(
        Array $config,
        Logger $logger,
        Container $session,
        FormService $serviceForm,
        FormCodeGroupService $serviceFormCodeGroup,
        FormCodeListService $serviceFormCodeList,
        FormCodeListGroupMapService $serviceFormCodeListGroupMap,
        FormCodeListPairMapService $serviceFormCodeListPairMap,
        FormCodePairService $serviceFormCodePair,
        HeadTitle $helperHeadTitle)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->serviceForm = $serviceForm;  
        $this->serviceFormCodeGroup = $serviceFormCodeGroup;
        $this->serviceFormCodeList = $serviceFormCodeList;
        $this->serviceFormCodeListGroupMap = $serviceFormCodeListGroupMap;
        $this->serviceFormCodeListPairMap = $serviceFormCodeListPairMap;
        $this->serviceFormCodePair = $serviceFormCodePair;

        parent::__construct();

        $this->view->helperHeadTitle = $helperHeadTitle;
    }
    
    public function indexAction()
    {
        $this->view->helperHeadTitle->append(' - Assign "Code Pair Lists" to "Forms"');
        $this->view->csrf = $this->getCsrfElement();
        $this->layout()->setTemplate('layout/metrics');
        $this->view->formIds = $this->serviceForm->getFormIdNamePairs(NULL, NULL);
        return  $this->view;
    }
    
    public function formCodeListsJsonAction()
    {
        $request = $this->getRequest();
        $response = '';
        
        $formId = $this->params()->fromQuery('formId');
        
        if ($this->validateCsrfToken($this->params()->fromQuery('csrf'))) {
            $formId = $this->params()->fromQuery('formId');
            $result = ['formId' => $formId];

            if (!empty($formId)) {
                $result['associated-lists'] = $this->serviceForm->getAssocLists($formId);
                $result['unassociated-lists'] = $this->serviceForm->getUnAssocLists($formId);
                
                $relatedFormNames = $this->serviceForm->getFormNamesRelatedByGroup($formId);
                if (count($relatedFormNames) > 0) {
                    $result['forms-related-by-groupId'] = $relatedFormNames;
                } else {
                    $result['forms-related-by-groupId'] = 'None';
                }
            }
         } else {
            $result = $this->getInvalidCSRFJsonResponse();
        }
        return $this->json->setVariables($result);
    }
    
    public function listCodePairsJsonAction()
    {
        if ($this->validateCsrfToken($this->params()->fromQuery('csrf'))) {
            $formId = $this->params()->fromQuery('formId');
            $listId = $this->params()->fromQuery('listId');

            $result = [
                'listId' => $listId,
                'code-pairs' => [],
                'forms-related-by-listId' => 'None'
            ];

            if (!empty($listId)) {
                $result['code-pairs'] = $this->serviceFormCodeList->getCodePairs($listId);
                
                $relatedFormNames = $this->serviceForm->getFormNamesRelatedByList($formId, $listId);
                if (count($relatedFormNames) > 0) {
                    $result['forms-related-by-listId'] = $relatedFormNames;
                } else {
                    $result['forms-related-by-listId'] = 'None';
                }
            }
         } else {
            $result = $this->getInvalidCSRFJsonResponse();
        }

        return $this->json->setVariables($result);
    }
    
    public function submitUpdatesJsonAction()
    {
        $request = $this->getRequest();
        $inputParams = (array) $request->getPost();
        if ($this->validateCsrfToken($inputParams['csrf'])) {
           $allForms =  ($this->request->getPost('allForms')== 'true');
           $updates =  $this->request->getPost('updates');
           
           //$this->logger->log("Update all forms? " . $allForms);
          // $this->logger->log("Updates: " . print_r($updates,true));
           
            $result = NULL;

            try {
                if ($allForms) {
                    $this->updateAll($updates);
                } else {
                    $this->updateOne($updates);
                } 
                
                $result = array('status' => true);
            } catch (Exception $e) {
                //$this->logger->log("Exception " . $e->getMessage());
                $result = [
                    'status' => false,
                    'message' => $e->getCode() . ':' . $e->getMessage()
                ];
            }
         } else {
            $result = $this->getInvalidCSRFJsonResponse();
        } 
        return $this->json->setVariables($result);
    }
    
    public function updateOne($updates)
    {
        $lists = []; //list array for the this form
        $formId = $updates['formId'];
        $formInfo = $this->serviceForm->getFormInfo($formId);
        
        $groupId = isset($formInfo['formCodeGroupId'])? $formInfo['formCodeGroupId'] : NULL;
        //$groupId = $formInfo['formCodeGroupId'];
        $assocLists = $this->serviceForm->getAssocLists($groupId); 

        //new lists
        if (isset($updates['newLists'])) {
            foreach ($updates['newLists'] as $newList) {
                $lists[] = [
                    'name' => $newList['name'],
                    'codePairs' => isset($newList['dirtyCodePairs'])
                        ? $newList['dirtyCodePairs'] : NULL
                ];
            }
        }

        //removed lists
        if (isset($updates['removedListIds'])) {
            foreach ($updates['removedListIds'] as $removedListId) {
                unset($assocLists[$removedListId]);
            }
        }
        
        //updated lists
        if (isset($updates['updatedLists'])) {
            foreach ($updates['updatedLists'] as $updList) {
                $listId = $updList['id'];
                $cleanCodePairIds = [];

                if (!array_key_exists($listId, $assocLists))
                    continue;

                if (array_key_exists('name', $updList)) { //name update
                    $assocLists[$listId] = $updList['name'];
                }

                $codePairs = [];
                $updDirtyCodePairMap = [];
                if (isset($updList['dirtyCodePairs'])) {
                    foreach ($updList['dirtyCodePairs'] as $dirtyCodePair) {
                        if ($dirtyCodePair['id'] > 0) { //updated ones
                            $updDirtyCodePairMap[$dirtyCodePair['id']] = $dirtyCodePair;
                        } else { //new code pairs
                            $codePairs[] = $dirtyCodePair;
                        }
                    }
                }

                foreach ($this->serviceFormCodeList->getCodePairs($listId) as $codePair) {
                    if (isset($updList['removedCodePairIds']) &&
                            in_array($codePair['id'], $updList['removedCodePairIds']))
                        continue;

                    if (isset($updDirtyCodePairMap[$codePair['id']])) {
                        $codePairs[] = array_merge($codePair,
                            $updDirtyCodePairMap[$codePair['id']]
                        );
                    } else {
                        $cleanCodePairIds[] = $codePair['id'];
                    }
                }

                $lists[] = [
                    'name' => $assocLists[$listId],
                    'codePairs' => $codePairs,
                    'cleanCodePairIds' => $cleanCodePairIds
                ];

                unset($assocLists[$listId]);
            }
        }
        //copy remaining associated lists
        foreach ($assocLists as $listId => $name) {
            $codePairs = [];
            foreach ($this->serviceFormCodeList->getCodePairs($listId) as $codePair) {
                $codePairs[] = [
                    'code' => $codePair['code'],
                    'value' => $codePair['value']
                ];
            }

            $lists[] = [
                'name' => $name,
                'codePairs' => $codePairs
            ];
        }

        $newListIds = [];
        $newGroupDesc = '';
        foreach ($lists as $index => $list) {
            $newGroupDesc .= ($index > 0 ? ',' : '') . $list['name'];
            $newListId = $this->serviceFormCodeList->insertList($list['name']);
            $newCodePairs = $list['codePairs'];
            $newCodePairIds = $this->manageCodePairAssoc($newCodePairs, NULL
            );
            if (isset($list['cleanCodePairIds'])) {
                $newCodePairIds = array_merge($newCodePairIds, $list['cleanCodePairIds']);
            }
            $this->serviceFormCodeListPairMap->insertAssoc($newListId, $newCodePairIds);
            $newListIds[] = $newListId;
        }

        $newGroupId = $this->serviceFormCodeGroup->insertGroup($newGroupDesc);
        $this->serviceFormCodeListGroupMap->insertAssoc($newGroupId, $newListIds);
        $this->serviceForm->updateGroup($formId, $newGroupId);
    }
    
    public function updateAll($updates)
    {
        $formId = $updates['formId'];
        $formInfo = $this->serviceForm->getFormInfo($formId);    
        $groupId = isset($formInfo['formCodeGroupId'])? $formInfo['formCodeGroupId'] : NULL;

        //new lists
        if (isset($updates['newLists'])) {
            $newListIds = [];
            foreach ($updates['newLists'] as $newList) {
                $newListId = $this->serviceFormCodeList->insertList($newList['name']);
                $newCodePairIds = $this->manageCodePairAssoc(
                    isset($newList['dirtyCodePairs']) ? $newList['dirtyCodePairs'] : NULL,
                    NULL
                );
                $this->serviceFormCodeListPairMap->insertAssoc($newListId, $newCodePairIds);
                $newListIds[] = $newListId;
            }
            if (count($newListIds) > 0) {
                $this->serviceFormCodeListGroupMap->insertAssoc($groupId, $newListIds);
            }
        }

        //updated lists
        if (isset($updates['updatedLists'])) {
            foreach ($updates['updatedLists'] as $updList) {
                if (array_key_exists('name', $updList)) {
                    $this->serviceFormCodeList->updateList($updList['id'], $updList['name']);
                }
                $newCodePairIds = $this->manageCodePairAssoc(
                    isset($updList['dirtyCodePairs']) ? $updList['dirtyCodePairs'] : NULL,
                    isset($updList['removedCodePairIds']) ? $updList['removedCodePairIds'] : NULL
                );
                $this->serviceFormCodeListPairMap->insertAssoc($updList['id'], $newCodePairIds);
            }
        }
        //removed lists
        if (isset($updates['removedListIds'])) {
            $this->serviceFormCodeListGroupMap->removeAssoc($groupId, $updates['removedListIds']);
        }
    }
    
    private function manageCodePairAssoc($codePairs, $removedCodePairIds)
    {
        $newCodePairIds = [];
        if (!is_null($codePairs)) {
            foreach ($codePairs as $codePair) {
                if (!isset($codePair['id']) || $codePair['id'] < 0) {
                    $newCodePairIds[] =
                        $this->serviceFormCodePair->insertPair($codePair['code'],
                            $codePair['value']
                        );
                } else {
                    $this->serviceFormCodePair->updatePair($codePair['id'],
                        isset($codePair['code']) ? $codePair['code'] : NULL,
                        isset($codePair['value']) ? $codePair['value'] : NULL
                    );
                }
            }
        }

        if (!is_null($removedCodePairIds)) {
            foreach ($removedCodePairIds as $codePairId) {
                $this->serviceFormCodePair->deletePair($codePairId);
            }
        }
        return $newCodePairIds;
    }
}
