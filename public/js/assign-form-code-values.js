/**
 *
 * @copyright (c) 2010 by LexisNexis Asset Company. All rights reserved.
 *
 * eCrash v3.0
 */

$(function(){
	
	if (typeof console == 'undefined') {
		console = {log:function(){}};
	}

	var fcp = (function(){
		
		var formId = null; /* Currently selected "Form Id" */
		var assocLists = {}; /* Lists associated to current Form */
		var selListId = null; /* Currently selected "List Id" */
		var unAssocLists = {}; /* Un-Associated lists for current Form */
		var removedListIds = {};
		
		function clear() {
			formId = null;
			assocLists = {};
			selListId = null;
			unAssocLists = {};
			removedListIds = {};
		}
		
		function setFormId(_formId) {
			//console.log('setFormId:', _formId);
			formId = _formId;
		}
		
		function getFormId() {
			return formId;
		}
		
		function setSelListId(listId) {
			selListId = listId;
		}
		
		function getSelListId() {
			return selListId;
		}
		
		function addList(formId, listId, listName) {			
			console.log('addList:', formId, listId, listName);
			
			if (listId == null) {
				listId = _rndIdGenFunc(function(rndId) {
					return !(rndId in assocLists || rndId in unAssocLists);
				});
			}
			else if (listId in removedListIds) { //Restored list
				delete removedListIds[listId];
			}
			
			var list = new FCList(listId, listName);
			
			if (formId != null) {
				assocLists[listId] = list;
			}
			else {
				unAssocLists[listId] = list;
			}
			
			return list;
		}
		
		function getList(listId) {
			if (listId in assocLists) {
				return assocLists[listId];
			}
			else if (listId in unAssocLists) {
				return unAssocLists[listId];
			}
			else {				
				return null;
			}
		}
		
		function getSelList() {
			return getList(getSelListId());
		}
		
		function updateList(listId, newListName) {
			//console.log('updateList:', listId, newListName);
			if (listId in assocLists) {
				$.extend(assocLists[listId], 
					{
						name: newListName
					}
				);
			}
		}
		
		function updateFormsRelatedByListId(listId, formsRelatedByListId) {
			$.extend(getList(listId), {formsRelatedByListId: formsRelatedByListId});
		}
		
		function removeList(listId) {
			//console.log('removeList:', listId);			
			if (!(listId in assocLists))
				return;

			if (listId > 0) {
				//Copy only 'existing' lists
				unAssocLists[listId] = assocLists[listId];
				removedListIds[listId] = listId;
			}
			
			delete assocLists[listId];

			if ( listId == selListId ) {
				selListId = null;
			}
		}
		
		function validate() {
			var valid = true;			
			$.each(assocLists, function() {
				valid = this.validate();
				return valid;
			});			
			return valid;
		}
		
		function getUpdates() {			
			var newLists = [];
			var updatedLists = [];
			var removedLists = [];
				
			$.each(assocLists, function() {
				if (this.id < 0) { //new list
					newLists = newLists.concat(this.delta());
				}
				else if (this.isDirty()) {
					updatedLists = updatedLists.concat(this.delta());
				}
			});
			
			$.each(removedListIds, function(key, value){
				removedLists.push(key); //just ids
			});
			
			return {
				formId: formId,
				dirty: (newLists.length + updatedLists.length + removedLists.length) > 0,
				newLists: newLists,
				updatedLists: updatedLists,
				removedListIds: removedLists
			};
		}
		
		//Internal functions
		function _rndIdGenFunc(stopSearchFunc) {
			var newId = 0;
			do {
				//Negative Id for locally added lists
				newId = -(Math.floor(Math.random() * 1000));
				//console.log('new random = ' + newId);
			} while(newId == 0 || !stopSearchFunc(newId));

			return newId;
		}
		
		//List class
		function FCList(listId, listName) {
			this.id = listId;
			this.name = listName;
			this.originalName = listName; //backup
			this.formsRelatedByListId = '';
			this.codePairsCached = (listId < 0); /* Locally created lists have negative ids */
			this.codePairs = {}
			this.removedCodePairIds = {};			
		}
		
		FCList.prototype.isDirty = function() {
			
			if (this.id < 0 || this.originalName != this.name) { //new list or name change
				return true;
			}
			else {
				var dirty = false;
				
				//Removed codepairs? Wish there was a count method...
				$.each(this.removedCodePairIds, function(){
					dirty = true;
					return false; //a single entry is enough
				});
				
				if (!dirty) {
					//codepairs dirty?
					$.each(this.codePairs, function(){
						dirty = this.isDirty();
						return !dirty;
					});
				}
				
				return dirty;
			}
		};
		
		FCList.prototype.getRow = function() {
			return [this.id, this.name];
		};
		
		FCList.prototype.validate = function() {
			//console.log('list.validate', this);
            //TODO : Vlaidation 
			console.log('')
			 if (this.name == null || $.trim(this.name).length == 0) {
				//TODO check against univ list names
				alert('"Form List Name" should not be empty');
				fcpui.focusListName(this.id);
				return false;
			} 

			var valid = true;
			var listId = this.id;

			$.each(this.codePairs, function(){
				if (this.code == null || $.trim(this.code).length == 0) {
					if (fcp.getSelListId() != listId) {
						fcpui.focusListName(listId);
					}
					alert('"Form Code" should not be empty');					
					fcpui.focusCode(this.id);
					valid = false;
					return false;
				}

				if (this.value == null || $.trim(this.value).length == 0) {
					if (fcp.getSelListId() != listId) {
						fcpui.focusListName(listId);
					}
					alert('"Form Code Value" should not be empty');
					fcpui.focusValue(this.id);
					valid = false;
					return false;
				}

				return valid;
			});
			
			return valid;
		};
		
		FCList.prototype.delta = function() {
			var dirtyCodePairs = [];
			$.each(this.codePairs, function(){
				if (this.isDirty()) {
					dirtyCodePairs = dirtyCodePairs.concat(this.delta());
				}
			});
			
			var removedCodePairs = [];
			$.each(this.removedCodePairIds, function(key, value) {
				removedCodePairs.push(key); //just ids
			});
			
			var retList = {
				id: this.id
			};
			
			if (this.name != this.originalName) {
				retList['name'] = this.name;
			}
			
			if (dirtyCodePairs.length > 0) {
				retList['dirtyCodePairs'] = dirtyCodePairs;
			}
			
			if (removedCodePairs.length > 0) {
				retList['removedCodePairIds'] = removedCodePairs;
			}

			return retList;
		};
		
		FCList.prototype.addCodePair = function(newCodePairId, code, value) {
			//console.log('list.addCodePair:', newCodePairId, code, value);			
			if (newCodePairId == null) {
				var myCodePairs = this.codePairs;
				newCodePairId = _rndIdGenFunc(function(rndId) {
					return !(rndId in myCodePairs);
				});
			}
			else if (newCodePairId in this.removedCodePairIds) { //Restored codepair
				delete this.removedCodePairIds[newCodePairId];
			}
			
			var newCodePair = new FCCodePair(newCodePairId, code, value);
			this.codePairs[newCodePairId] = newCodePair;			
			return newCodePair;
		};
		
		FCList.prototype.updateCodePair = function(codePairId, code, value) {
			//console.log('list.updateCodePair:', codePairId, code, value);
			$.extend(this.codePairs[codePairId], 
				{
					code: code, 
					value: value
				}
			);
		};
		
		FCList.prototype.removeCodePair = function(codePairId) {
			//console.log('list.removeCodePair:', codePairId);
			delete this.codePairs[codePairId];			
			if (codePairId > 0) {				
				this.removedCodePairIds[codePairId] = codePairId;
			}
			return true;
		};
		
		
		//CodePair class
		function FCCodePair(newCodePairId, code, value) {
			this.id = newCodePairId;
			this.code = code;
			this.originalCode = code;
			this.value = value;
			this.originalValue = value;
		}
		
		FCCodePair.prototype.isDirty = function() {
			return this.id < 0 //new list
				|| this.originalCode != this.code 
				|| this.originalValue != this.value;
		};
		
		FCCodePair.prototype.getRow = function() {
			return [this.id, this.code, this.value];
		};
		
		FCCodePair.prototype.validate = function() {
			var emptyCode = ($.trim(this.code).length == 0);
			var emptyValue = ($.trim(this.value).length == 0);
			
			if (!emptyCode && !emptyValue) {
				return true;
			}
			
			if (emptyCode) {
				alert('Empty Code');
				fcpui.focusCode(this.id);
			}
			else {
				alert('Empty Value');
				fcpui.focusValue(this.id);
			}

			return false;
		};		
		
		FCCodePair.prototype.delta = function() {
			
			var retCodePair = {
				id: this.id
			};
			
			if (this.code != this.originalCode) {
				retCodePair['code'] = this.code;
			}
			
			if (this.value != this.originalValue) {
				retCodePair['value'] = this.value;
			}
			
			return retCodePair;
		};		
		
		return {
			clear: clear,
			setFormId: setFormId,
			getFormId: getFormId,
			setSelListId: setSelListId,
			getSelListId: getSelListId,
			addList: addList,
			getList: getList,
			getSelList: getSelList,
			updateList: updateList,
			updateFormsRelatedByListId: updateFormsRelatedByListId,
			removeList: removeList,
			validate: validate,
			getUpdates: getUpdates
		};
				
	})();
	window.fcp = fcp;
	
	var pd = window.progressDialogManager.create();
    
    var formListTable = $('#formListTable').dataTable({
		bPaginate: false,
		bInfo: false,
		bAutoWidth: false,
		bJQueryUI: true,
		bFilter: false, 
		aoColumns: [
			{
				bSortable: false,
                mRender: function(data, type, row, meta ) {
					return  '<input type="checkbox" ' + 
						' name="listCheckbox"   value="' + row[0] + '"/>';
				}
			},
			{
				bSortable: false, 
				sWidth: '100%',
				sClass: 'form_name',
				mRender: function(data, type, row, meta ) {
					return  '<input type="text" ' + 
						' name="listName"   value="' + row[1] + '"/>';
				}
			}
		],
		aaSorting: [[1, 'asc']],
		sDom: '<"H"<"ecToolbar"<"#listActionsDiv">><"#formNameDiv">fr>t<"F"ip>'
	});

	$('.ecToolbar #listActionsDiv')
		.append('<input id="btnAddList" type="button" value="Add"/>')
		.append('<input id="btnRemoveList" type="button" value="Remove"/>');
	
    var listCodePairsTable = $('#listCodePairsTable').dataTable({
		bPaginate: false,
		bInfo: false,
		bAutoWidth: false,
		bJQueryUI: true,
		bFilter: false, 
        aaSorting: false, 
		aoColumns: [
			{
				bSortable: false,
                mRender: function(data, type, row, meta ) {
					return  '<input type="checkbox" ' + 
						' name="codePairCheckbox"   value="' + row[0] + '"/>';
				}
			},
			{
				bSortable: false, 
				sWidth: '10%',
				sClass: 'form_name',
				mRender: function(data, type, row, meta ) {
					return  '<input type="text" ' + 
						' name="code"   value="' + row[1] + '"/>';
				}
			},
			{
				bSortable: false, 
				sWidth: '90%',
				sClass: 'form_name',
				mRender: function(data, type, row, meta ) {
					return  '<input type="text" ' + 
						' name="value"   value="' + row[2] + '"/>';
				}
			}
		],
		//aaSorting: [[1, 'asc']],
		sDom: '<"H"<"ecToolbar"<"#codePairActionsDiv">><"#formListNameDiv">fr>t<"F"ip>'
	});
	
	$('.ecToolbar #codePairActionsDiv')
		.append('<input id="btnAddCodePair" type="button" value="Add"/>')
		.append('<input id="btnRemoveCodePair" type="button" value="Remove"/>');
	
	//Adding style to toolbar buttons	
	$('.ecToolbar input[type=button]').addClass('ui-button ui-state-default');

	var fcpui = {
		loadLists: function(formId) {		
			//console.log('loadList:', formId);

			//Clear
			fcp.clear();
			formListTable.fnClearTable(formId.length == 0);
			listCodePairsTable.fnClearTable(true);
			$('#formsRelatedByGroupId').text('');
			$('#formListNameDiv').text('');
			$('#listCodePairsDiv').hide();

			if (formId.length == 0) {
				$('#formNameDiv').text('');
				$('#formListsDiv').hide();
				return;
			}

			pd.show('Loading associated lists');
			$('#formListsDiv').hide();

			var successFunc = function(result) {
				
				if (!hasCsrfError(result)) {

					$('#formListsDiv').show();
					fcp.setFormId(result['formId']);				
					$('#formsRelatedByGroupId').html(fcpui.createUL(result['forms-related-by-groupId']));
					$('#formNameDiv').text($.trim($('#formSelect option:selected').text()) + "'s Lists");

					$.each(result['associated-lists'],
						function(listId, listName) {
							var list = fcp.addList(formId, listId, listName, false);
							formListTable.fnAddData(list.getRow(), false);
						}
					);

					$.each(result['unassociated-lists'],
						function(listId, listName) {
							fcp.addList(null, listId, listName, false);
						}
					);

					formListTable.fnDraw(true); //To account for empty rows
				}
				
				pd.hide();
			};

			var errorFunc = function (xhr, ajaxOptions, thrownError) {				
				//console.log('loadLists:JSON Failure - ', xhr.status, ajaxOptions, thrownError);
				formListTable.fnClearTable(true);
				pd.hide();
			};

			$.ajax({
				type: 'GET',
				url: window.baseUrl + '/admin/assign-form-code-values/form-code-lists-json',
				dataType: 'json',
				success: successFunc,
				error: errorFunc,
				data: {
					formId: formId,
					csrf: $('#csrf').val()
				}
			});
		},
		loadCodePairs: function(formTD){

			//UX: Select one row logic
			$('#formListTable .row_selected').removeClass('row_selected');
			formTD.parent().addClass('row_selected');
			
			var listNameText = formTD.children('input[name=listName]');

			$('#listCodePairsDiv').hide();
			$('#formListNameDiv').text(listNameText.val());
			$('#formsRelatedByListId').text('');
			
			//Clear
			listCodePairsTable.fnClearTable(false);
			
			var listId = fixedEncodeURIComponent(formTD.siblings().children('input[name=listCheckbox]').val());
			fcp.setSelListId(listId);

			//Check cache
			var cachedList = fcp.getList(listId);
			if (cachedList != null && cachedList['codePairsCached']) {
				$('#formsRelatedByListId').html(cachedList['formsRelatedByListId']);
				$.each(cachedList['codePairs'],
					function() {
						listCodePairsTable.fnAddData(this.getRow());
					}
				);
				listCodePairsTable.fnDraw(true); //To account for empty rows
				$('#listCodePairsDiv').show();
				return;
			}
			else {
				
			}
			
			pd.show("Loading code pairs");
			
			var successFunc = function(result) {
				
				if (!hasCsrfError(result)) {
					if (fcp.getSelListId() != result.listId) {
						pd.hide();
						return;
					}

					var formsRelatedByListId = fcpui.createUL(result['forms-related-by-listId']);
					$('#formsRelatedByListId').html(formsRelatedByListId);
					fcp.updateFormsRelatedByListId(result.listId, formsRelatedByListId);

					$.each(result['code-pairs'],
						function() {
							var codePair = fcp.getList(result.listId).addCodePair(
								this['id'], this['code'], this['value'], false
							);
							listCodePairsTable.fnAddData(codePair.getRow());
						}
					);

					$.extend(cachedList, {codePairsCached: true});
					listCodePairsTable.fnDraw(true); //To account for empty rows

					//To gain back focus if this load was triggered by text focus
					listNameText.focus();
					$('#listCodePairsDiv').show();
				}

				pd.hide();
			};
			
			var errorFunc = function (xhr, ajaxOptions, thrownError) {
				//console.log("loadCodePairs:JSON Failure - ", xhr.status, ajaxOptions, thrownError);
				listCodePairsTable.fnClearTable(true);
				pd.hide();
			};
				
                
                console.log(listId);
			$.ajax({
				type: 'GET',
				url: window.baseUrl + '/admin/assign-form-code-values/list-code-pairs-json',
				dataType: 'json',
				success: successFunc,
				error: errorFunc,
				data: {
					formId: fcp.getFormId(),
					listId: listId,
					csrf: $('#csrf').val()
				}
			});
		},
		focusListName: function(listId) {
			$('#formListTable input[name=listCheckbox][value=' + listId + ']')
				.closest('tr').find('input[type=text]').focus();
		},
		focusCode: function(codePairId) {
			$('#listCodePairsTable input[name=codePairCheckbox][value=' + codePairId + ']')
				.closest('tr').find('input[name=code]').focus();
		},
		focusValue: function(codePairId) {
			$('#listCodePairsTable input[name=codePairCheckbox][value=' + codePairId + ']')
				.closest('tr').find('input[name=value]').focus();
		},
		submitUpdates: function(allForms) {
			console.log('submitUpdates:', allForms);
			
			if (!fcp.validate()) {
				return;
			}
			
			var updates = fcp.getUpdates();
			console.log('submitUpdates:updates =\n', JSON.stringify(updates));
			
			if (!updates.dirty) {
				alert('Nothing to save');
				return;				
			}
			
			pd.show('Saving update...');				
			var curFormId = fcp.getFormId();

			var successFunc = function(result) {
				pd.hide();
				if (!hasCsrfError(result)) {
					

					if (result['status']) {
						fcp.clear();
						alert('Changes updated successfully.');
						fcpui.loadLists(curFormId);
					}
					else {
						alert('Failed to update changes.');
					}
				}
			};

			var errorFunc = function (xhr, ajaxOptions, thrownError) {
				//console.log("submitUpdates: JSON Failure - ", xhr.status, ajaxOptions, thrownError);
				pd.hide();
			};

			$.ajax({
				type: 'POST',
				url: window.baseUrl + '/admin/assign-form-code-values/submit-updates-json',
				dataType: 'json',
				success: successFunc,
				error: errorFunc,
				data: {
					allForms: allForms,
					updates: updates,
					csrf: $('#csrf').val()
				}
			});				
		},
		createUL: function(items) {
			if (!$.isArray(items)) {
				return items;
			}
			
			items = $.map(items, function(value) {
				return '<li>' + value + '</li>';
			});			
			return '<ul>' + items.join('') + '</ul>';
		}
	};
	window.fcpui = fcpui;

	//Data load Actions
	$('#formSelect').change(function(){
		
		//To handle the situation when we programmatically revert the change
		if (fcp.getFormId() == $(this).val()) 
			return;
		
		if (fcp.getUpdates().dirty) {
			var response = confirm('Unsaved changes will be lost. Do you want to continue?');
			if (!response) {
				$(this).val(fcp.getFormId());
				return;
			}
		}
		
		fcpui.loadLists($(this).val());
	});
	
   
    
    $(document).on('focusin', '#formListTable td.form_name', function(e) {
        fcpui.loadCodePairs($(this));
    });
    
    //UX Actions
     $(document).on('mouseover', '#formListTable td', function(e) {
        $(this).parent().addClass('row_hover');
    });
    
     $(document).on('mouseout', '#formListTable td', function(e) {
        $(this).parent().addClass('row_hover');
    });
    


	//Add / Remove Actions
	$('#btnAddList').on('click', function() {			
		var list = fcp.addList(fcp.getFormId(), null, '', true);
		formListTable.fnAddData(list.getRow(), true)
	});
	
	$('#btnRemoveList').on('click', function() {
		$.each($('input[name=listCheckbox]:checked'), 
			function() {
				var listId = $(this).val();
				
				if (listId == fcp.getSelListId()) {
					$('#formListNameDiv').text('');
					listCodePairsTable.fnClearTable(true);
					$('#listCodePairsDiv').hide();
				}				
				
				fcp.removeList(listId);
				formListTable.fnDeleteRow($(this).closest('tr')[0]);
			}
		);
	});
	
	$('#btnAddCodePair').on('click', function() {
		var newCodePair = fcp.getSelList().addCodePair(null, '', '', true);
		listCodePairsTable.fnAddData(newCodePair.getRow());
	});
	
	$('#btnRemoveCodePair').on('click', function() {
		$.each($('input[name=codePairCheckbox]:checked'), 
			function() {				
				fcp.getSelList().removeCodePair($(this).val());
				listCodePairsTable.fnDeleteRow($(this).closest('tr')[0]);
			}
		);
	});
	

	//Update Actions
	 $(document).on('change', '#formListTable input[name=listName]', function(e) {
			//fcp.updateList(fcp.getSelListId(), $(this).val());
            var listID = $(this).closest('tr').find('input[type=checkbox]').val();
            fcp.updateList(listID, $(this).val());
			$('#formListNameDiv').text($(this).val());				
		}
	);
	
     $(document).on('change', '#listCodePairsTable input[name=code], #listCodePairsTable input[name=value]', function(e) {
			var tr = $(this).closest('tr');
			var codePairId = tr.find('input[type=checkbox]').val();
			var code = tr.find('input[name=code]').val();
			var value = tr.find('input[name=value]').val();
			
			fcp.getSelList().updateCodePair(codePairId, code, value);
		}
	);
	

	//Submit form changes Actions	
	$('#saveForm').on('click', function() {
		fcpui.submitUpdates(false);
	});
	
	$('#saveAllForms').on('click', function() {
		fcpui.submitUpdates(true);
	});
	
});
