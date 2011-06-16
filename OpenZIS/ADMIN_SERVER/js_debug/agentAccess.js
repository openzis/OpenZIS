// Zones Javascript File
var access = {};

access.ZONEID;
access.AGENTID;
access.NEWACCESSGRID;
access.CONTEXTID = 1;

access.showAddPermissionGroup = function(){
    var groups = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({
            url: './grouppermission/getgroupszone?ZONE_ID='+access.ZONEID+'&lic='+main.LIC,
            method: 'POST'
        }),
        reader: new Ext.data.JsonReader({
            root: 'objs'
        }, [{
            name: 'id',
            mapping: 'id'
        }, {
            name: 'name',
            mapping: 'name'
        }])
    });
	
    var permissionGroupForm = new Ext.FormPanel({
        labelWidth:120,
        url:'./grouppermission/agentusegroup',
        frame:true,
        width:275,
        defaultType:'textfield',
        monitorValid:true,
        items:[new
        Ext.form.ComboBox({
            fieldLabel:'Group',
            width: 120,
            store: groups,
            typeAhead: false,
            displayField: 'name',
            valueField: 'id',
            forceSelection:true,
            editable:false,
            mode: 'remote',
            selectOnFocus:true,
            emptyText: 'Selete Group...',
            hiddenName: 'GROUP_ID',
            triggerAction: 'all',
            allowBlank:false
        }),
        new
        Ext.form.Checkbox({
            fieldLabel:'Override Current Permissions',
            name:'OVERRIDE',
            value:access.ZONEID,
            allowBlank:false
        }),
        new
        Ext.form.Hidden({
            name:'AGENT_ID',
            value:access.AGENTID
        }),
        new
        Ext.form.Hidden({
            name:'lic',
            value:main.LIC
        }),
        new
        Ext.form.Hidden({
            name:'ZONE_ID',
            value:access.ZONEID
        })],
        buttons:[{
            text:'Use Group',
            formBind: true,
            handler:function(){
                permissionGroupForm.getForm().findField('lic').setValue(main.LIC);
				permissionGroupForm.getForm().submit({
                    method:'POST',
                    waitTitle:'Connecting',
                    waitMsg:'Sending data...',
                    success:function(form, action)
                    {
                        agent.getAgentDetails(access.ZONEID, access.AGENTID);
                        win.close();
                        Ext.MessageBox.alert('Success', 'Permissions Added');
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
                win.destroy();
            }
        }]
    });
	
    if(win){
        win.destroy();
    }
    win = new Ext.Window({
        layout:'fit',
        width:330,
        height:150,
        closable: false,
        resizable: false,
        plain: true,
        modal: true,
        title: 'Use Permssion Group',
        items: [permissionGroupForm]
    });
    win.show();
};

access.showAddPermission = function(){
	
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
            url: './dataobject/getdataobjectgroups?ZONE='+access.ZONEID+'&lic='+main.LIC,
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
	
    var addAgentPermission = new Ext.FormPanel({
        labelWidth:100,
        url:'./agentaccess/savenewpermission', 
        frame:true, 
        title:'', 
        width:230, 
        defaultType:'textfield',
        monitorValid:true,
        items:[new Ext.form.Hidden({
            name:'ZONE_ID',
            value:access.ZONEID
        }),new Ext.form.Hidden({
            name:'AGENT_ID',
            value:access.AGENTID
        }),new Ext.form.Hidden({
            name:'lic',
            value:main.LIC
        }),new Ext.form.Hidden({
            name:'CONTEXT_ID',
            value:access.CONTEXTID
        }),new Ext.form.ComboBox({
            id:'_workingGroupsSelect',
            fieldLabel:'Working Groups',
            width: 350,
            store: dataObjectGroups,
            typeAhead: false,
            forceSelection:true,
            editable:false,
            displayField: 'display',
            valueField: 'value',
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
            mode: 'remote',
            forceSelection:true,
            editable:false,
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
                addAgentPermission.getForm().findField('lic').setValue(main.LIC);
				addAgentPermission.getForm().submit({
                    method:'POST',
                    waitTitle:'Connecting',
                    waitMsg:'Sending data...',
                    success:function(form, action)
                    {
                        agent.getAgentDetails(access.ZONEID, access.AGENTID);
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
        width:750,
        height:200,
        closable: false,
        resizable: false,
        plain: true,
        modal: true,
        title: 'Add Permission',
        items: [addAgentPermission]
    });
    win.show();
	
    Ext.getCmp('_workingGroupsSelect').on('select', function(){
        var dataObjectsSelect = Ext.getCmp('_dataObjectSelectBox');
		
        if(dataObjectsSelect.store == null){
            dataObjectsSelect.store = null;
            var newStore = new Ext.data.Store({
                proxy: new Ext.data.HttpProxy({
                    url: './dataobject/getdataobjects?ID='+Ext.getCmp('_workingGroupsSelect').getValue()+'&lic='+main.LIC,
                    method: 'POST'
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

access.updatePermission = function(permissionId){
    var provide      = access.checkBoxConversion(document.getElementById('provide_'+permissionId));
    var subscribe    = access.checkBoxConversion(document.getElementById('subscribe_'+permissionId));
    var request      = access.checkBoxConversion(document.getElementById('request_'+permissionId));
    var respond      = access.checkBoxConversion(document.getElementById('respond_'+permissionId));
    var add          = access.checkBoxConversion(document.getElementById('add_'+permissionId));
    var change	     = access.checkBoxConversion(document.getElementById('change_'+permissionId));
    var delete_      = access.checkBoxConversion(document.getElementById('delete_'+permissionId));
    main.clearTabs();
    main.addTab_item(main.insertLoadingPanel());
	
    Ext.Ajax.request({
        url: './agentaccess/updatepermission',
        method:'post',
        params: {
            PERMISSION_ID  :permissionId,
            PROVIDE	       :provide,
            SUBSCRIBE      :subscribe,
            REQUEST        :request,
            RESPOND        :respond,
            ADD            :add,
            CHANGE         :change,
            DELETE         :delete_,
			lic			   :main.LIC
        },
        success:function(response){
            agent.getAgentDetails(access.ZONEID, access.AGENTID);
            Ext.MessageBox.alert('Success', 'Permission Updated');
        },
        failure:function(response){
            Ext.Msg.alert('Error!','Error Updating Permssion');
        }
    });
};

access.buildCurrentAgentPermissions = function(permissions, zoneId, agentId){
    access.ZONEID  = zoneId;
    access.AGENTID = agentId;
	
    var length = permissions.length;
    var data = '[';
    for(var i = 0; i < length; i++){
		
        data += "['"+permissions[i].objectName+"',";
        data += "'"+access.tableRowCheckBoxHelper(permissions[i].provide,'provide_'+permissions[i].permissionId)+"',";
        data += "'"+access.tableRowCheckBoxHelper(permissions[i].subscribe,'subscribe_'+permissions[i].permissionId)+"',";
        data += "'"+access.tableRowCheckBoxHelper(permissions[i].request,'request_'+permissions[i].permissionId)+"',";
        data += "'"+access.tableRowCheckBoxHelper(permissions[i].respond,'respond_'+permissions[i].permissionId)+"',";
        data += "'"+access.tableRowCheckBoxHelper(permissions[i].add,'add_'+permissions[i].permissionId)+"',";
        data += "'"+access.tableRowCheckBoxHelper(permissions[i].update,'change_'+permissions[i].permissionId)+"',";
        data += "'"+access.tableRowCheckBoxHelper(permissions[i].delete_,'delete_'+permissions[i].permissionId)+"',";
        data += "'<a href=\"javascript:access.updatePermission("+permissions[i].permissionId+");\">Update</a>'";
        if(i == (length - 1)){
            data += "]";
        }
        else{
            data += "],";
        }
    }
    data += ']';
	
    var dataObj = eval(data);
    return grids.createAccessControlGrid(dataObj);
};

access.checkBoxConversion = function(elem){
    if(elem.checked){
        return '1';
    }
    else{
        return '0';
    }
};

access.tableRowCheckBoxHelper = function(allowed, id){
    if(allowed == 1){
        return '<input id="'+id+'" checked="true" type="checkbox" />';
    }
    else{
        return '<input id="'+id+'" type="checkbox" />';
    }
};

access.dataObjectSelected = function(elem){
    if(elem.value == 0){
        access.NEWACCESSGRID.hide();
    }
    else{
        access.NEWACCESSGRID.show();
    }
};

