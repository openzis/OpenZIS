// Zones Javascript File

var zones = {};

zones.zoneInfo;
zones.agents;
zones.versions = new Array();
zones.pushMessages;
zones.receivedMessages;
zones.ZONE_ID;

zones.showAddZone = function(){
    var versions = zones.getVersionDataStore();
	
    var addZoneForm = new Ext.FormPanel({
        labelWidth:80,
        url:'./zone/addzone', 
        frame:true, 
        width:230, 
        defaultType:'textfield',
        monitorValid:true,
        items:[new Ext.form.ComboBox({
            fieldLabel:'Auth Type',
            width: 160,
            store: new Ext.data.SimpleStore({
                fields: ['authenticationValue', 'authenticationDesc'],
                data : [['0','No Authentication'],['1','Username and Password'],['2','Certificate']]
            }),
            typeAhead: false,
            forceSelection:true,
            editable:false,
            displayField: 'authenticationDesc',
            valueField: 'authenticationValue',
            mode: 'local',
            selectOnFocus:true,
            emptyText: 'Authentication Type...',
            hiddenName: 'ZONEAUTHENTICATIONTYPE',
            triggerAction: 'all',
            allowBlank:false
        }),
        new Ext.form.ComboBox({
            fieldLabel:'SIF Version',
            width: 120,
            store: versions,
            forceSelection:true,
            editable:false,
            typeAhead: false,
            displayField: 'desc',
            valueField: 'id',
            mode: 'local',
            selectOnFocus:true,
            emptyText: 'SIF Version..',
            hiddenName: 'VERSION_ID',
            allowBlank:false,
            triggerAction: 'all'
        }),{
            width: 120,
            fieldLabel:'Description',
            name:'DESCRIPTION',
            emptyText:'Zone Description',
            allowBlank:false
        },
        {
            width: 120,
            fieldLabel:'Source ID',
            name:'SOURCE_ID',
            emptyText:'Zone Source ID',
            allowBlank:false
        },	new Ext.form.Hidden({
		            name: 'lic'
		})
        ],
        buttons:[{
            text:'Add Zone',
            formBind: true,
            handler:function(){
				addZoneForm.getForm().findField('lic').setValue(main.LIC);
                addZoneForm.getForm().submit({
                    method:'POST',
                    waitTitle:'Connecting',
                    waitMsg:'Sending data...',
                    success:function(form, action)
                    {
                        var obj = Ext.util.JSON.decode(action.response.responseText);
                        if(obj.success)
                        {
                            Ext.MessageBox.alert('Success', 'Zone Added Successfully.');
                            win.close();
                            zones_root.reload();
                        }
                        else
                        {
                            Ext.Msg.alert('Error!','Error Adding Agent');
                        }
                    },
                    failure:function(form, action)
                    {
                        if(action.failureType == 'server')
                        {
                            Ext.Msg.alert('Error!', "Error Adding Zone");
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
        width:290,
        height:185,
        closable: false,
        resizable: false,
        plain: true,
        modal: true,
        title:'Add Zone',
        items: [addZoneForm]
    });
    win.show();
};

zones.showUpdateZone = function(sourceId, desc, id, versionId, zoneAuthType){
    versions = zones.getVersionDataStore();
	
    var updateZoneForm = new Ext.FormPanel({
        labelWidth:80,
        url:'./zone/updatezone', 
        frame:true, 
        title:'', 
        width:230, 
        defaultType:'textfield',
        monitorValid:true,
        items:[new Ext.form.ComboBox({
            fieldLabel:'Auth Type',
            width: 160,
            store: new Ext.data.SimpleStore({
                fields: ['authenticationValue', 'authenticationDesc'],
                data : [['0','No Authentication'],['1','Username and Password'],['2','Certificate']]
            }),
            typeAhead: false,
            displayField: 'authenticationDesc',
            valueField: 'authenticationValue',
            mode: 'local',
            selectOnFocus:true,
            emptyText: 'Authentication Type...',
            hiddenName: 'ZONEAUTHENTICATIONTYPE',
            triggerAction: 'all',
            allowBlank:false,
            value:zoneAuthType
        }),new
        Ext.form.ComboBox({
            fieldLabel:'SIF Version',
            width: 120,
            store: versions,
            typeAhead: false,
            displayField: 'desc',
            valueField: 'id',
            mode: 'local',
            selectOnFocus:true,
            allowBlank:false,
            emptyText: 'SIF Version..',
            hiddenName: 'VERSION_ID',
            triggerAction: 'all',
            value:versionId
        }),new Ext.form.Hidden({
            name: 'ID',
            value: id
        }),	new Ext.form.Hidden({
		           name: 'lic'
		}),{
            width: 120,
            fieldLabel:'Description',
            name:'DESCRIPTION',
            emptyText:'Zone Description',
            allowBlank:false,
            value:desc
        },{
            width: 120,
            fieldLabel:'Source ID',
            name:'SOURCE_ID',
            emptyText:'Zone Source ID',
            allowBlank:false,
            value:sourceId
        }],
        buttons:[{
            text:'Update Zone',
            formBind: true,
            handler:function(){
                updateZoneForm.getForm().findField('lic').setValue(main.LIC);
				updateZoneForm.getForm().submit({
                    method:'POST',
                    waitTitle:'Connecting',
                    waitMsg:'Sending data...',
                    success:function(form, action)
                    {
                        obj = Ext.util.JSON.decode(action.response.responseText);
                        if(obj.success)
                        {
                            Ext.MessageBox.alert('Success', 'Zone Updated Successfully.');
                            win.close();
                            zones_root.reload();
                            zones.getZoneInformation(id);
                        }
                        else
                        {
                            Ext.Msg.alert('Error!','Error Updating Zone');
                        }
                    },
                    failure:function(form, action)
                    {
                        if(action.failureType == 'server')
                        {
                            Ext.Msg.alert('Error!', "Error Updating Zone");
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
        width:290,
        height:185,
        closable: false,
        resizable: false,
        plain: true,
        modal: true,
        title: 'Update Zone',
        items: [updateZoneForm]
    });
    win.show();

};

zones.getAllZones = function(){
    main.clearTabs();
    main.addTab_item(main.insertLoadingPanel());
	
    Ext.Ajax.request({
        url: './zone/getzones',
        method:'post',
        params: {lic:main.LIC},
        success:function(response){
            obj = Ext.util.JSON.decode(response.responseText);
            if(obj.success)
            {
                zones.buildZoneList(obj);
            }
            else
            {
                Ext.Msg.alert('Error!','Error Getting Zones');
            }
        },
        failure:function(response){
            Ext.Msg.alert('Error!','Error Getting Zones');
        }
    });
};

zones.buildZoneList = function(obj){
    var zones = obj.zones;
    if(zones == null || zones.length == 0){
        main.clearTabs();
        main.addTab('<h2>No Zones Created</h2>','Zones');
    }
    else{
        var length = zones.length;
        var data = '[';
        var desc;
        var sleeping;
        var sourceId;
        for(var i = 0; i < length; i++){
            desc     = zones[i].zoneDesc.replace(/'/gi, "\\'");
            sourceId = zones[i].sourceId.replace(/'/gi, "\\'");
            if(zones[i].sleeping == 1){
                sleeping = 'Sleeping';
            }
            else{
                sleeping = 'Awake';
            }
            data += "['<a href=\"javascript:zones.getZoneInformation("+zones[i].zoneId+")\">"+sourceId+"</a>',";
            data += "'"+desc+"',";
            data += "'"+sleeping+"',";
            data += '\''+zones[i].numAgents+'\',';
            data += '\''+zones[i].numMessages+'\',';
            data += "'<a href=\"javascript:zones.archiveMessages("+zones[i].zoneId+")\">Archive Messages</a>'";
            if(i == (length - 1)){
                data += "]";
            }
            else{
                data += "],";
            }
        }
        data += ']';
		
        var dataObj = eval(data);
		
        main.clearTabs();
        main.addTab_item(grids.createZoneListGrid(dataObj));
    }
};

zones.showAddAgent = function(){
    var agents = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({
            url: './agent/getagents?lic='+main.LIC,
            method: 'POST',
			params: {
	        }
        }),
        reader: new Ext.data.JsonReader({
            root: 'agents'
        }, [{
            name: 'id',
            mapping: 'id'
        }, {
            name: 'name',
            mapping: 'name'
        }])
    });

    var addAgentZoneForm = new Ext.FormPanel({
        labelWidth:80,
        url:'./zone/addagent', 
        frame:true, 
        title:'', 
        width:400, 
        defaultType:'textfield',
        monitorValid:true,
        items:[new
        Ext.form.ComboBox({
            fieldLabel:'Agent',
            width: 250,
            store: agents,
            typeAhead: false,
            displayField: 'name',
            valueField: 'id',
            mode: 'remote',
            selectOnFocus:true,
            emptyText: 'Selete Agent...',
            hiddenName: 'AGENT_ID',
            triggerAction: 'all',
            allowBlank:false
        }),new Ext.form.Hidden({
            name: 'ZONE_ID',
            value: zones.ZONE_ID
        }),new Ext.form.Hidden({
            name: 'lic'
        })],
        buttons:[{
            text:'Add Agent',
            formBind: true,
            handler:function(){
				addAgentZoneForm.getForm().findField('lic').setValue(main.LIC);
                addAgentZoneForm.getForm().submit({
                    method:'POST',
                    waitTitle:'Connecting',
                    waitMsg:'Sending data...',
                    success:function(form, action)
                    {
                        obj = Ext.util.JSON.decode(action.response.responseText);
                        if(obj.success)
                        {
                            zones.getZoneInformation(zones.ZONE_ID);
                            Ext.MessageBox.alert('Success', 'Agent Added Successfully.');
                            win.close();
                            zones_root.reload();
                        }
                        else
                        {
                            Ext.Msg.alert('Error!', "Error Adding Agent");
                        }
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
        width:400,
        height:120,
        closable: false,
        resizable: false,
        plain: true,
        modal: true,
        title: 'Add Agent',
        items: [addAgentZoneForm]
    });
    win.show();
	
};

zones.getZoneInformation = function(zoneId){
    main.clearTabs();
    main.addTab_item(main.insertLoadingPanel());
	
    Ext.Ajax.request({
        url: './zone/getzonestatus',
        method:'post',
        params: {
            ID:zoneId,
            lic:main.LIC
        },
        success:function(response){
            obj = Ext.util.JSON.decode(response.responseText);
            if(obj.success)
            {
                zones.ZONE_ID = zoneId;
                zones.buildZoneInformation_HTML(obj, zoneId);
            }
            else
            {
                Ext.Msg.alert('Error!','Error Getting Zone Information');
            }
        },
        failure:function(response){
            Ext.Msg.alert('Error!','Error Getting Zone Information');
        }
    });
};

zones.showPushMessageXml = function(index, type){
    /*
	 1 = rec
	 2 = sent
	*/
	
    if(type == 1){
        var html = "<div align='center'><textarea cols='70' rows='18'>"+zones.pushMessages[index].recMessage+"</textarea></div>";
        main.showWindow_noBtn('Recieved Message', html, 350, 595);
    }
    else{
        var html = "<div align='center'><textarea cols='70' rows='18'>"+zones.pushMessages[index].sentMessage+"</textarea></div>";
        main.showWindow_noBtn('Sent Message', html, 350, 595);
    }
	
};

zones.showReceivedMessageXml = function(index, type){
    /*
	 1 = rec
	 2 = sent
	*/
	Ext.Ajax.request({
		url: 	'./zone/getxmlmessage',
		method: 'post',
		params: {
			id:index,
			type:type,
			lic:main.LIC },
		success:function(response){
			var recMessage = response.responseText;
			var html = "<div align='center'><textarea cols='70' rows='18'>"+recMessage+"</textarea></div>";
			if (type ==1 ) {
				var title = 'Recieved Message';
			} else {
				var title = 'Sent Message';	
			}
			main.showWindow_noBtn(title, html, 350, 595);
		},
		failure:function(response){
		    Ext.Msg.alert('Error!','Error Getting Zones');
		}
		
		});
	
/*    if(type == 1){
        var html = "<div align='center'><textarea cols='70' rows='18'>"+zones.receivedMessages[index].recMessage+"</textarea></div>";
        main.showWindow_noBtn('Recieved Message', html, 350, 595);
    }
    else{
        var html = "<div align='center'><textarea cols='70' rows='18'>"+zones.receivedMessages[index].sentMessage+"</textarea></div>";
        main.showWindow_noBtn('Sent Message', html, 350, 595);
    }
*/	
};

zones.buildPushedMessageDataArray = function(){
    var length = zones.pushMessages.length;
    var data = '[';
    for(var i = 0; i < length; i++){
        data += '[\''+zones.pushMessages[i].timestamp+'\',';
        data += '\''+zones.pushMessages[i].agentName+'\',';
        data += '\''+zones.pushMessages[i].messageType+'\',';
        data += '\'<a href="javascript:zones.showPushMessageXml('+zones.pushMessages[i].logId+',2);">View</a>\',';
        data += '\'<a href="javascript:zones.showPushMessageXml('+zones.pushMessages[i].logId+',1);">View</a>\'';
        if(i == (length - 1)){
            data += "]";
        }
        else{
            data += "],";
        }
    }
    data += ']';
    var dataObj = eval(data);
    return dataObj;
};

zones.buildReceivedMessageDataArray = function(){
    var length = zones.receivedMessages.length;
    var data = '[';
    for(var i = 0; i < length; i++){
        data += '[\''+zones.receivedMessages[i].timestamp+'\',';
        data += '\''+zones.receivedMessages[i].agentName+'\',';
        data += '\''+zones.receivedMessages[i].messageType+'\',';
        data += '\'<a href="javascript:zones.showReceivedMessageXml('+zones.receivedMessages[i].logId+',1);">View</a>\',';
        data += '\'<a href="javascript:zones.showReceivedMessageXml('+zones.receivedMessages[i].logId+',2);">View</a>\'';
        if(i == (length - 1)){
            data += "]";
        }
        else{
            data += "],";
        }
    }
    data += ']';
    var dataObj = eval(data);
    return dataObj;
};

zones.buildAgentsDataArray = function(agents, zoneId){
	
    var length = agents.length;
    var data = '[';
    for(var i = 0; i < length; i++){
        data += "['<a href=\"javascript:agent.getAgentDetails("+zoneId+", "+agents[i].agentId+");\">"+agents[i].sourceId+"</a>',";
        data += "'"+agents[i].agentDesc+"',";
        data += '\''+agents[i].status+'\',';
        data += '\''+agent.AwakeConvertor(agents[i].sleeping)+'\',';
        data += '\''+agents[i].numMessages+'\',';
        data += "'<a href=\"javascript:zones.removeAgent("+zoneId+", "+agents[i].agentId+");\">Remove</a>'";
        if(i == (length - 1)){
            data += "]";

        }
        else{
            data += "],";
        }
    }
    data += ']';
    var dataObj = eval(data);
    return dataObj;
};

zones.buildZoneInformation_HTML = function(obj, zoneId){
    var openZisPushHandler = obj.openZisPushHandler;
    var zoneInfo  = obj.zone;
    var zoneAuthTypeDesc;
    var sleeping;
    if(zoneInfo[0].sleeping == 1){
        sleeping = 'Sleeping';
    }
    else{
        sleeping = 'Awake';
    }
    if(zoneInfo[0].zoneAuthenticationType == 0)
    {
        zoneAuthTypeDesc = 'No Authentication';
    }
    else
    if(zoneInfo[0].zoneAuthenticationType == 1)
    {
        zoneAuthTypeDesc = 'Username and Password';
    }
    else
    if(zoneInfo[0].zoneAuthenticationType == 2)
    {
        zoneAuthTypeDesc = 'Certificate';
    }
	
    var zoneHtml = '<table style="font-family:\'Courier New\', Courier, monospace;padding:10px;">'+
    '<tr><td colspan="2" style="font-size:18px;font-weight:bold;color:#666666;">Zone Details</td></tr>'+
    '<tr><td style="font-weight:bold;">Description:</td><td>'+zoneInfo[0].zoneDesc+'</td></tr>'+
    '<tr><td style="font-weight:bold;">Source Id:</td><td>'+zoneInfo[0].sourceId+'</td></tr>'+
    '<tr><td style="font-weight:bold;">Zone Url:</td><td>'+zoneInfo[0].zoneUrl+'</td></tr>'+
    '<tr><td style="font-weight:bold;">Auth Type:</td><td>'+zoneAuthTypeDesc+'</td></tr>'+
    '<tr><td style="font-weight:bold;">Status:</td><td>'+sleeping+'&nbsp;<a href="javascript:zones.putZoneToSleep('+zoneId+', '+zoneInfo[0].sleeping+');">Change</a></td></tr>'+
    '<tr><td style="font-weight:bold;">SIF Version:</td><td>'+zoneInfo[0].version+'</td></tr>'+
    '<tr><td style="font-weight:bold;">Created By:</td><td>'+zoneInfo[0].creator+'</td></tr>'+
    '<tr><td style="font-weight:bold;">Created:</td><td>'+zoneInfo[0].create+'</td></tr>';
    if(zoneInfo[0].update == '' || zoneInfo[0].update == null){
        zoneHtml += '<tr><td style="font-weight:bold;">Last Update:</td><td>N/A</td></tr>';
    }
    else{
        zoneHtml += '<tr><td style="font-weight:bold;">Last Update:</td><td>'+zoneInfo[0].update+'</td></tr>';
    }
    var name     = zoneInfo[0].zoneDesc.replace(/'/gi, "\\'");
    var sourceId = zoneInfo[0].sourceId.replace(/'/gi, "\\'");
				  
    zoneHtml += '<tr><td style="font-weight:bold;"># Agents:</td><td>'+zoneInfo[0].numAgents+'</td></tr>'+
    '<tr><td colspan="2">&nbsp;</td></tr><tr><td colspan="2" >'+
    '<input type="button" value="Update" class="button" onclick="zones.showUpdateZone(\''+sourceId+'\', \''+name+'\', '+zoneInfo[0].zoneId+', '+zoneInfo[0].versionId+', '+zoneInfo[0].zoneAuthenticationType+');" />'+
    '&nbsp;<input type="button" value="Delete" class="button" onclick="Ext.MessageBox.confirm(\'Confirm\', \'Are you sure you want to delete this zone?\', zones.deleteZone);" />'+
    '</td></tr></table><hr/>';
							  
    zoneHtml += '<table style="font-family:\'Courier New\', Courier, monospace;padding:10px;">'+
    '<tr><td colspan="2" style="font-size:18px;font-weight:bold;color:#666666;">Push Information</td></tr>'+
    '<tr><td style="font-weight:bold;">Last Start:</td><td>'+((openZisPushHandler[0].lastStart == null) ? 'N/A' : openZisPushHandler[0].lastStart)+'</td></tr>'+
    '<tr><td style="font-weight:bold;">Last Stop:</td><td>'+((openZisPushHandler[0].lastStop == null) ? 'N/A' : openZisPushHandler[0].lastStop)+'</td></tr>'+
    '<tr><td style="font-weight:bold;">Push Every:</td><td>'+((openZisPushHandler[0].sleepTimeSeconds == null) ? 'N/A' : openZisPushHandler[0].sleepTimeSeconds)+' seconds</td></tr>'+
    '<tr><td style="font-weight:bold;">Running:</td><td id="pushActiveHolder">'+main.yesNoConvertor(openZisPushHandler[0].pushRunning);
							  
    if(openZisPushHandler[0].pushRunning == 1){
        zoneHtml += ' <a href="javascript:zones.stopPush();">Stop</a>';
    }
    else{
        zoneHtml += ' <a href="javascript:zones.showStartPush();">Start</a>';
    }
				  
    zoneHtml += '</td></tr></table>';
				
	
    zones.pushMessages = zoneInfo[0].pushedMessages;
    zones.receivedMessages = zoneInfo[0].receivedMessages;
	
    main.clearTabs();
    main.addTab_item(grids.createZoneAgentListGrid(zones.buildAgentsDataArray(obj.agents, zoneId)));
    main.addTab_item(grids.createZoneReceivedMessageGrid(zones.buildReceivedMessageDataArray()));
    main.addTab_item(grids.createZonePushedMessageGrid(zones.buildPushedMessageDataArray()));
    main.addTab(zoneHtml,'Zone Details');
    viewport.doLayout();
};

zones.deleteZone = function(btn){
    if(btn == 'yes'){
        Ext.Ajax.request({
            url: './zone/deletezone',
            method:'post',
            params: {
                zone_id:zones.ZONE_ID,
				lic:main.LIC
            },
            success:function(response){
                obj = Ext.util.JSON.decode(response.responseText);
                if(obj.success)
                {
                    zones_root.reload();
                    main.clearTabs();
                    Ext.MessageBox.alert('Success', "Zone Deleted");
                }
                else
                {
                    Ext.Msg.alert('Error!','Error Deleting Zone');
                }
            },
            failure:function(response){
                Ext.Msg.alert('Error!','Error Deleting Zone');
            }
        });
    }
};

zones.refreshZoneReceivedMessages = function(){
    Ext.MessageBox.alert('Loading....', "Refreshing Messages....");
	
    var elem = tabPanel.getActiveTab();
	
    Ext.Ajax.request({
        url: './zone/getzonemessages',
        method:'post',
        params: {
            ZONE_ID:zones.ZONE_ID,
            ZIT_LOG_MESSAGE_TYPE:2,
			lic:main.LIC
        },
        success:function(response){
            obj = Ext.util.JSON.decode(response.responseText);
            if(obj.success)
            {
                zones.receivedMessages = obj.messages;
                var grid = grids.createZoneReceivedMessageGrid(zones.buildReceivedMessageDataArray());
                tabPanel.remove(elem);
                tabPanel.insert(1, grid).show();
                Ext.MessageBox.alert('Success', "Messages Refreshed");
            }
            else
            {
                Ext.Msg.alert('Error!','Error Refreshing Messages');
            }
        },
        failure:function(response){
            Ext.Msg.alert('Error!','Error Refreshing Messages');
        }
    });
};

zones.refreshZonePushMessages = function(){
    Ext.MessageBox.alert('Loading....', "Refreshing Messages....");
	
    var elem = tabPanel.getActiveTab();
	
    Ext.Ajax.request({
        url: './zone/getzonemessages',
        method:'post',
        params: {
            ZONE_ID:zones.ZONE_ID,
            ZIT_LOG_MESSAGE_TYPE:1,
			lic:main.LIC
        },
        success:function(response){
            obj = Ext.util.JSON.decode(response.responseText);
            if(obj.success)
            {
                zones.pushMessages = obj.messages;
                var grid = grids.createZonePushedMessageGrid(zones.buildPushedMessageDataArray());
                tabPanel.remove(elem);
                tabPanel.insert(2, grid).show();
                Ext.MessageBox.alert('Success', "Messages Refreshed");
            }
            else
            {
                Ext.Msg.alert('Error!','Error Refreshing Messages');
            }
        },
        failure:function(response){
            Ext.Msg.alert('Error!','Error Refreshing Messages');
        }
    });
};

zones.archiveMessages = function(zoneId){
    Ext.MessageBox.alert('Loading....', "Archiving Messages....");

    var elem = tabPanel.getActiveTab();
	
    Ext.Ajax.request({
        url: './zone/archivemessages',
        method:'post',
        params: {
            ZONE_ID:zoneId,
			lic:main.LIC
        },
        success:function(response){
            obj = Ext.util.JSON.decode(response.responseText);
            if(obj.success)
            {
                zones.getAllZones();
                Ext.MessageBox.alert('Success', "Messages Archived");
            }
            else
            {
                Ext.Msg.alert('Error!','Error Messages');
            }
        },
        failure:function(response){
            Ext.Msg.alert('Error!','Error Messages');
        }
    });
}

zones.removeAgent = function(zoneId, agentId){
    Ext.Ajax.request({
        url: './zone/removeagent',
        method:'post',
        params: {
            ZONE_ID:zoneId,
            AGENT_ID:agentId,
            CONTEXT_ID:1,
			lic:main.LIC
        },
        success:function(response){
            obj = Ext.util.JSON.decode(response.responseText);
            if(obj.success)
            {
                Ext.MessageBox.alert('Success', "Agent Removed");
                zones.getZoneInformation(zoneId);
                zones_root.reload();
            }
            else
            {
                Ext.Msg.alert('Error!','Error Removing Agent');
            }
        },
        failure:function(response){
            Ext.Msg.alert('Error!','Error Removing Agent');
        }
    });
};

zones.putZoneToSleep = function(zoneId, currentVal){
    var newVal;
    var message;
    if(currentVal == 1){
        newVal = 2;
        message = "Zone Woken Up";
    }
    else{
        newVal = 1;
        message = "Zone Put To Sleep";
    }
	
    Ext.Ajax.request({
        url: './zone/putzonetosleep',
        method:'post',
        params: {
            ID:zoneId,
            SLEEP_VAL:newVal,
			lic:main.LIC
        },
        success:function(response){
            obj = Ext.util.JSON.decode(response.responseText);
            if(obj.success)
            {
                Ext.MessageBox.alert('Success', message);
                zones.getZoneInformation(zoneId);
            }
            else
            {
                Ext.Msg.alert('Error!','Error Updating Zones Status');
            }
        },
        failure:function(response){
            Ext.Msg.alert('Error!','Error Updating Zones Status');
        }
    });
};

zones.getVersionDataStore = function(){
    var simpleDataStore = new Ext.data.SimpleStore({
        fields: ['id', 'desc']
    });
    versions = zones.versions;
    var length = versions.length;
    for(var i = 0; i < length; i++){
        simpleDataStore.add(new Ext.data.Record(versions[i]));
    }
    return simpleDataStore;
};

zones.stopPush = function(){
    Ext.Ajax.request({
        url: './zone/stoppush',
        method:'post',
        params: {
            ZONE_ID:zones.ZONE_ID,
            CONTEXT_ID:1,
			lic:main.LIC
        },
        success:function(response){
            obj = Ext.util.JSON.decode(response.responseText);
            if(obj.success)
            {
                Ext.MessageBox.alert('Success', "Push Stopped");
                zones.getZoneInformation(zones.ZONE_ID);
            }
            else
            {
                Ext.Msg.alert('Error!','Error Updating Zones Status');
            }
        },
        failure:function(response){
            Ext.Msg.alert('Error!','Error Updating Zones Status');
        }
    });
};

zones.getPushTimeIntervalDataStore = function(){
    var timeIntervals = new Array();
    timeIntervals.push({
        id:5,
        desc:"5"
    });
    timeIntervals.push({
        id:10,
        desc:"10"
    });
	
    var simpleDataStore = new Ext.data.SimpleStore({
        fields: ['id', 'desc']
    });
    var length = timeIntervals.length;
    for(var i = 0; i < length; i++){
        simpleDataStore.add(new Ext.data.Record(timeIntervals[i]));
    }
    return simpleDataStore;
};

zones.getPushTimeFrameDataStore = function(){
    var timeFrames = new Array();
    timeFrames.push({
        id:0,
        desc:"Seconds"
    });
    timeFrames.push({
        id:1,
        desc:"Minutes"
    });
    timeFrames.push({
        id:2,
        desc:"Hours"
    });
	
    var simpleDataStore = new Ext.data.SimpleStore({
        fields: ['id', 'desc']
    });
    var length = timeFrames.length;
    for(var i = 0; i < length; i++){
        simpleDataStore.add(new Ext.data.Record(timeFrames[i]));
    }
    return simpleDataStore;
};

zones.showStartPush = function(){
	
    var startPushWindow = new Ext.FormPanel({
        labelWidth:80,
        url:'./zone/startpush', 
        frame:true, 
        title:'', 
        width:230, 
        items:[new
        Ext.form.ComboBox({
            fieldLabel:'Interval',
            width: 75,
            store: zones.getPushTimeIntervalDataStore(),
            typeAhead: false,
            displayField: 'desc',
            valueField: 'id',
            mode: 'local',
            selectOnFocus:true,
            emptyText: 'Interval...',
            hiddenName: 'TIME_INTERVAL',
            allowBlank:false,
            triggerAction: 'all'
        }),new
        Ext.form.ComboBox({
            fieldLabel:'Time Frame',
            width: 100,
            store: zones.getPushTimeFrameDataStore(),
            typeAhead: false,
            displayField: 'desc',
            valueField: 'id',
            mode: 'local',
            selectOnFocus:true,
            emptyText: 'Time Frame...',
            hiddenName: 'TIME_FRAME',
            allowBlank:false,
            triggerAction: 'all'
        }),new Ext.form.Hidden({
            name: 'ZONE_ID',
            value: zones.ZONE_ID
        }),new Ext.form.Hidden({
            name: 'CONTEXT_ID',
            value: '1'
        }),new Ext.form.Hidden({
           name: 'lic',
           value: '1'
       	})],
        buttons:[{
            text:'Start',
            formBind: true,
            handler:function(){
                startPushWindow.getForm().findField('lic').setValue(main.LIC);
				startPushWindow.getForm().submit({
                    method:'POST',
                    waitTitle:'Connecting',
                    waitMsg:'Sending data...',
                    success:function(form, action)
                    {
                        obj = Ext.util.JSON.decode(action.response.responseText);
                        if(obj.success)
                        {
                            Ext.MessageBox.alert('Success', 'Push Started');
                            zones.getZoneInformation(zones.ZONE_ID);
                            win.close();
                        }
                        else
                        {
                            Ext.Msg.alert('Error!','Error Starting Push');
                        }
                    },
                    failure:function(form, action)
                    {
                        if(action.failureType == 'server')
                        {
                            Ext.Msg.alert('Error!', "Error Starting Push");
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
        height:140,
        closable: false,
        resizable: false,
        plain: true,
        modal: true,
        title:'Start Push',
        items: [startPushWindow]
    });
    win.show();
};

zones.getZoneMessages = function(zoneId){
    main.clearTabs();
	main.addTab_item(msg.xmlMessages(zoneId));
//	main.addTab_item(grids.createZoneReceivedMessageGrid(zones.buildReceivedMessageDataArray()));
//    main.addTab_item(grids.createZonePushedMessageGrid(zones.buildPushedMessageDataArray()));
	viewport.doLayout();
	
};