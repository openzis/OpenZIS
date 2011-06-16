//group permission js

groupPermission = {};

groupPermission.VERSION;
groupPermission.GROUP_ID;

groupPermission.deleteGroup = function(groupId){
    Ext.Ajax.request({
        url: './grouppermission/deletegroup',
        method:'post',
        params: {
            GROUP_ID:groupId,
			lic:main.LIC
        },
        success:function(response){
            groupPermission.getAllGroups();
            main.clearTabs();
            Ext.MessageBox.alert('Success', 'Group Deleted');
        },
        failure:function(response){
            myCode = response.responseText;
			if (myCode.substr(0,2) == "/*") {
			myCode = myCode.substring(2, myCode.length - 2);
			}
            obj = Ext.util.JSON.decode(myCode);
/*
			obj = Ext.util.JSON.decode(action.response.responseText);
*/
            Ext.Msg.alert('Error!', obj.errors.reason);
        }
    });
};

groupPermission.updatePermission = function(itemId){
    var provide      = access.checkBoxConversion(document.getElementById('provide_'+itemId));
    var subscribe    = access.checkBoxConversion(document.getElementById('subscribe_'+itemId));
    var request      = access.checkBoxConversion(document.getElementById('request_'+itemId));
    var respond      = access.checkBoxConversion(document.getElementById('respond_'+itemId));
    var add          = access.checkBoxConversion(document.getElementById('add_'+itemId));
    var change	     = access.checkBoxConversion(document.getElementById('change_'+itemId));
    var delete_      = access.checkBoxConversion(document.getElementById('delete_'+itemId));
    main.clearTabs();
    main.addTab_item(main.insertLoadingPanel());
	
    Ext.Ajax.request({
        url: './grouppermission/updategroupitem',
        method:'post',
        params: {
            ITEM_ID        :itemId,
            PROVIDE	       :provide,
            SUBSCRIBE      :subscribe,
            REQUEST        :request,
            RESPOND        :respond,
            ADD            :add,
            CHANGE         :change,
            DELETE         :delete_,
			lic				:main.LIC
        },
        success:function(response){
            groupPermission.getAllGroupItems(groupPermission.GROUP_ID,groupPermission.VERSION);
            Ext.MessageBox.alert('Success', 'Permission Updated');
        },
        failure:function(response){
            obj = Ext.util.JSON.decode(action.response.responseText);
            Ext.Msg.alert('Error!', obj.errors.reason);
        }
    });
};

groupPermission.showAddPermission = function(){
	
    var data = "[["+
    "'<input id=\"provide\" name=\"PROVIDE\" type=\"checkbox\" />',"+
    "'<input id=\"subscribe\" name=\"SUBSCRIBE\" type=\"checkbox\" />',"+
    "'<input id=\"request\" name=\"REQUEST\" type=\"checkbox\" />',"+
    "'<input id=\"respond\" name=\"RESPOND\" type=\"checkbox\" />',"+
    "'<input id=\"add\" name=\"ADD\" type=\"checkbox\" />',"+
    "'<input id=\"change\" name=\"CHANGE\" type=\"checkbox\" />',"+
    "'<input id=\"delete\" name=\"DELETE\" type=\"checkbox\" />',"+
    "]]";
    var dataObj = eval(data);
    access.NEWACCESSGRID = grids.createNewGroupPermissionItemGrid(dataObj);
	
    var dataObjectGroups = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({
            url: './dataobject/getdataobjectgroupsnozone?VERSION='+groupPermission.VERSION+'&lic='+main.LIC,
            method: 'POST'
        }),
        reader: new Ext.data.JsonReader({
            root: 'data'
        }, [{
            name: 'value',
            mapping: 'dataObjectGroupId'
        }, {
            name: 'display',
            mapping: 'dataObjectGroupDesc'
        }])
    });
	
    var addGroupItemForm = new Ext.FormPanel({
        labelWidth:100,
        url:'./grouppermission/addgroupitem', 
        frame:true, 
        title:'', 
        width:230, 
        defaultType:'textfield',
        monitorValid:true,
        items:[new Ext.form.Hidden({
            name:'GROUP_ID',
            value:groupPermission.GROUP_ID,
            id:'_groupId'
        })	,	new Ext.form.Hidden({
		            name: 'lic'
		    }),new Ext.form.ComboBox({
            id:'_workingGroupsSelect',
            fieldLabel:'Working Groups',
            width: 350,
            store: dataObjectGroups,
            typeAhead: false,
            displayField: 'display',
            valueField: 'value',
            forceSelection:true,
            editable:false,
            mode: 'remote',
            selectOnFocus:true,
            emptyText: 'Working Groups..',
            hiidenName:'workingGroupId',
            hiddenId: '_workGroupsId',
            triggerAction: 'all',
            allowBlank:false
        }),new Ext.form.ComboBox({
            fieldLabel:'Data Objects',
            width: 350,
            typeAhead: false,
            displayField: 'display',
            valueField: 'value',
            forceSelection:true,
            editable:false,
            mode: 'remote',
            selectOnFocus:true,
            emptyText: 'Data Object..',
            hiddenName: 'DATA_OBJECT_ID',
            triggerAction: 'all',
            allowBlank:false,
            id: '_dataObjectSelectBox',
            disabled:true
        }),access.NEWACCESSGRID],
        buttons:[{
            text:'Add Permission',
            formBind: true,
            handler:function(){
                addGroupItemForm.getForm().findField('lic').setValue(main.LIC);
				addGroupItemForm.getForm().submit({
                    method:'POST',
                    waitTitle:'Connecting',
                    waitMsg:'Sending data...',
                    success:function(form, action)
                    {
                        Ext.Msg.alert('Success','Group Permission Added Successfully');
                        groupPermission.getAllGroupItems(groupPermission.GROUP_ID,groupPermission.VERSION);
                        win.close();
                    },
                    failure:function(form, action)
                    {
                        if(action.failureType == 'server')
                        {
                            obj = Ext.util.JSON.decode(action.response.responseText);
                            Ext.Msg.alert('Error!', obj.errors.reason);
                        }
                        else
                        {
                            Ext.Msg.alert('Warning!', 'Server is unreachable');
                        }
                    }
                });
            }
        },
        {
            text: 'Cancel',
            formBind: false,
            handler:function(){
                win.close();
            }
        }]
    });	
	
    if(win){
        win.destroy();
    }
    win = new Ext.Window({
        layout:'fit',
        width:750,
        height:200,
        closable: false,
        resizable: false,
        plain: true,
        modal: true,
        title: 'Add Permission',
        items: [addGroupItemForm]
    });
    win.show();
	
    Ext.getCmp('_workingGroupsSelect').on('select', function(){
        var dataObjectsSelect = Ext.getCmp('_dataObjectSelectBox');
		
        if(dataObjectsSelect.store == null){
            dataObjectsSelect.store = null;
            var newStore = new Ext.data.Store({
                proxy: new Ext.data.HttpProxy({
                    url: './dataobject/getdataobjects?ID='+Ext.getCmp('_workingGroupsSelect').getValue()+'&lic='+main.LIC, 
					method:'post'
                }),
                reader: new Ext.data.JsonReader({
                    root: 'data'
                }, [{
                    name: 'value',
                    mapping: 'dataObjectId'
                }, {
                    name: 'display',
                    mapping: 'dataObjectName'
                }])
            });
            dataObjectsSelect.store = newStore;
        }
        else{
            dataObjectsSelect.reset();
            dataObjectsSelect.store.reload({
                params: {
                    ID: Ext.getCmp('_workingGroupsSelect').getValue()
                }
            });
        }
        dataObjectsSelect.enable();
    });
};

groupPermission.showAddGroup = function(){
    versions = zones.getVersionDataStore();
	
    var addGroupPermissionForm = new Ext.FormPanel({
        labelWidth:80,
        url:'./grouppermission/addgroup', 
        frame:true, 
        title:'', 
        width:230, 
        defaultType:'textfield',
        monitorValid:true,
        items:[new
        Ext.form.ComboBox({
            fieldLabel:'SIF Version',
            width: 120,
            store: versions,
            typeAhead: false,
            forceSelection:true,
            editable:false,
            displayField: 'desc',
            valueField: 'id',
            mode: 'local',
            selectOnFocus:true,
            emptyText: 'SIF Version..',
            hiddenName: 'VERSION',
            triggerAction: 'all',
            allowBlank:false
        })	,	new Ext.form.Hidden({
			            name: 'lic'
			    }),{
            width: 120,
            fieldLabel:'Group Name',
            name:'NAME',
            emptyText:'Group Name',
            allowBlank:false
        }],
        buttons:[{
            text:'Add Group',
            formBind: true,
            handler:function(){
                addGroupPermissionForm.getForm().findField('lic').setValue(main.LIC);
				addGroupPermissionForm.getForm().submit({
                    method:'POST',
                    waitTitle:'Connecting',
                    waitMsg:'Sending data...',
                    success:function(form, action)
                    {
                        Ext.Msg.alert('Success','Group Permission Added Successfully');
                        groupPermission.getAllGroups();
                        win.close();
                    },
                    failure:function(form, action)
                    {
                        if(action.failureType == 'server')
                        {
                            myCode = action.response.responseText;
							if (myCode.substr(0,2) == "/*") {
							myCode = myCode.substring(2, myCode.length - 2);
							}
				            obj = Ext.util.JSON.decode(myCode);
                            Ext.Msg.alert('Error!', obj.errors.reason);
                        }
                        else
                        {
                            Ext.Msg.alert('Warning!', 'Server is unreachable');
                        }
                    }
                });
            }
        },
        {
            text: 'Cancel',
            formBind: false,
            handler:function(){
                win.close();
            }
        }]
    });	
	
    if(win){
        win.destroy();
    }
    win = new Ext.Window({
        layout:'fit',
        width:300,
        height:130,
        closable: false,
        resizable: false,
        plain: true,
        modal: true,
        title: 'Add Group Permission',
        items: [addGroupPermissionForm]
    });
    win.show();
};

groupPermission.buildGroups = function(groups){
    if(groups.length == 0 || groups == null || groups == ''){
        var data = '[["No Group Permissions"," "," ", " "]]';
    }
    else{
        var length = groups.length;
        var data = '[';
        for(var i = 0; i < length; i++){
			
            var name = groups[i].name.replace(/'/gi, "\\'");
			
            data += "['"+name+"',";
            data += "'"+groups[i].version+"',";
            data += "'<a href=\"javascript:groupPermission.getAllGroupItems("+groups[i].id+", "+groups[i].versionId+");\">Permissions</a>',";
            data += "'<a href=\"javascript:groupPermission.deleteGroup("+groups[i].id+");\">Delete</a>'";
            if(i == (length - 1)){
                data += "]";
            }
            else{
                data += "],";
            }
        }
        data += ']';
    }
	
    var dataObj = eval(data);
	
    var grid = grids.createPermssionGroupListGrid(dataObj);
    try{
        groupPermissionPanel.remove(groupPermissionPanel.getComponent(0))
        }catch(ex){/*do nothing*/};
    groupPermissionPanel.add(grid);
    viewport.doLayout();
};

groupPermission.buildGroupsItems = function(permissions){
    if(permissions.length == 0 || permissions == null){
        var data = '[["No Permissions"," "," ", " ", " ", " ", " ", " ", " "]]';
    }
    else{
        var length = permissions.length;
        var data = '[';
        for(var i = 0; i < length; i++){
			
            data += "['"+permissions[i].objectName+"',";
            data += "'"+access.tableRowCheckBoxHelper(permissions[i].provide,'provide_'+permissions[i].id)+"',";
            data += "'"+access.tableRowCheckBoxHelper(permissions[i].subscribe,'subscribe_'+permissions[i].id)+"',";
            data += "'"+access.tableRowCheckBoxHelper(permissions[i].request,'request_'+permissions[i].id)+"',";
            data += "'"+access.tableRowCheckBoxHelper(permissions[i].respond,'respond_'+permissions[i].id)+"',";
            data += "'"+access.tableRowCheckBoxHelper(permissions[i].add,'add_'+permissions[i].id)+"',";
            data += "'"+access.tableRowCheckBoxHelper(permissions[i].update,'change_'+permissions[i].id)+"',";
            data += "'"+access.tableRowCheckBoxHelper(permissions[i].delete_,'delete_'+permissions[i].id)+"',";
            data += "'<a href=\"javascript:groupPermission.updatePermission("+permissions[i].id+");\">Update</a>'";
            if(i == (length - 1)){
                data += "]";
            }
            else{
                data += "],";
            }
        }
        data += ']';
    }
	
    var dataObj = eval(data);
	
    var grid = grids.createGroupPermissionItemsGrid(dataObj);
    main.clearTabs();
    main.addTab_item(grid);
    viewport.doLayout();
};

groupPermission.getAllGroupItems = function(groupId, versionId){
    groupPermission.VERSION  = versionId;
    groupPermission.GROUP_ID = groupId;
	
    Ext.Ajax.request({
        url: './grouppermission/getgroupsitems',
        method:'post',
        params: {
            GROUP_ID:groupId,
			lic:main.LIC
        },
        success:function(response){
            myCode = response.responseText;
			if (myCode.substr(0,2) == "/*") {
			myCode = myCode.substring(2, myCode.length - 2);
			}
            obj = Ext.util.JSON.decode(myCode);
            groupPermission.buildGroupsItems(obj.objs);
        },
        failure:function(response){
            myCode = response.responseText;
			if (myCode.substr(0,2) == "/*") {
			myCode = myCode.substring(2, myCode.length - 2);
			}
            obj = Ext.util.JSON.decode(myCode);
            Ext.Msg.alert('Error!', obj.errors.reason);
        }
    });
};

groupPermission.getAllGroups = function(){
    Ext.Ajax.request({
        url: './grouppermission/getgroups',
        method:'post',
        params: {lic:main.LIC},
        success:function(response){
            myCode = response.responseText;
			if (myCode.substr(0,2) == "/*") {
			myCode = myCode.substring(2, myCode.length - 2);
			}
            obj = Ext.util.JSON.decode(myCode);
            groupPermission.buildGroups(obj.objs);
        },
        failure:function(response){
            myCode = response.responseText;
			if (myCode.substr(0,2) == "/*") {
			myCode = myCode.substring(2, myCode.length - 2);
			}
            obj = Ext.util.JSON.decode(myCode);
            Ext.Msg.alert('Error!', obj.errors.reason);
        }
    });
};