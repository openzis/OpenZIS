
var zit = {};
zit.errorMessages;

zit.showUpdateZit = function(desc, sourceId, adminUrl, zitUrl, minBuffer, maxBuffer){
    var updateZitForm = new Ext.FormPanel({
        labelWidth:100,
        url:'./zit/updatezit',
        frame:true,
        width:275,
        defaultType:'textfield',
        monitorValid:true,
        items:[{
            fieldLabel:'Description',
            name:'ZIT_NAME',
            value:desc,
            allowBlank:false
        },{
            fieldLabel:'SourceId',
            name:'SOURCE_ID',
            value:sourceId,
            allowBlank:false
        },{
            fieldLabel:'ZIS Url',
            name:'ZIT_URL',
            value:zitUrl,
            allowBlank:true
        },{
            fieldLabel:'Admin Url',
            name:'ADMIN_URL',
            value:adminUrl,
            allowBlank:true
        },{
            fieldLabel:'Min Buffer',
            name:'MIN_BUFFER',
            value:minBuffer,
            allowBlank:true
        },{
            fieldLabel:'Max Buffer',
            name:'MAX_BUFFER',
            value:maxBuffer,
            allowBlank:true
        },{
	        xtype:'hidden',
			name:'lic'
	    }],
        buttons:[{
            text:'Update',
            formBind: true,
            handler:function(){
				updateZitForm.getForm().findField('lic').setValue(main.LIC);
                updateZitForm.getForm().submit({
                    method:'POST',
                    waitTitle:'Connecting',
                    waitMsg:'Sending data...',
                    success:function(form, action)
                    {
                        Ext.MessageBox.alert('Success', 'ZIS Updated');
                        zit.getZitInformation();
                        win.destroy();
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
                win.destroy();
            }
        }]
    });
	
    if(win){
        win.destroy();
    }
    win = new Ext.Window({
        layout:'fit',
        width:300,
        height:250,
        closable: false,
        resizable: false,
        plain: true,
        modal: true,
        title: 'Update ZIS',
        items: [updateZitForm]
    });
    win.show();
};

zit.putZitToSleep = function(currentVal){
    var newVal;
    var message;
    if(currentVal == 1){
        newVal = '0';
        message = "ZIS Woken Up";
    }
    else{
        newVal = '1';
        message = "ZIS Put To Sleep";
    }
	
    Ext.Ajax.request({
        url: './zit/putzittosleep',
        method:'post',
        params: {
            SLEEP_VAL:newVal, lic:main.LIC
        },
        success:function(response){
            Ext.MessageBox.alert('Success', message);
            zit.getZitInformation();
        },
        failure:function(response){
            Ext.Msg.alert('Error!','Error Changing ZIS Status');
        }
    });
};

zit.buildErrorMessageGrid = function(){
    var length = zit.errorMessages.length;
    var data = '[';
    for(var i = 0; i < length; i++){
		
        data += "['"+zit.errorMessages[i].timestamp+"',";
        data += "'"+zit.errorMessages[i].location+"',";
        data += "'"+zit.errorMessages[i].shortDescription+"',";
        data += "'"+zit.convertNullToNa(zit.errorMessages[i].zoneName)+"',";
        data += "'"+zit.convertNullToNa(zit.errorMessages[i].agentName)+"',";
        data += "'<a href=\"javascript:zit.showLongDescription("+i+")\">More Info</a>'";
        if(i == (length - 1)){
            data += "]";
        }
        else{
            data += "],";
        }
    }
    data += ']';
	
    var dataObj = eval(data);
	
    return grids.createErrorMessagesGrid(dataObj);
};

zit.showLongDescription = function(index){
    var longErrorDescription = zit.errorMessages[index].longDescription;
	
    var html = "<div align='center'><textarea style='overflow:auto;' cols='70' rows='18'>"+longErrorDescription+"</textarea></div>";
    main.showWindow_noBtn('Error Description', html, 360, 595);
};

zit.buildZitInformation = function(obj){
    var zitObj = obj.zit[0];
	
    var displayUpdateZone = "none";
    var zitName  = zitObj.zitName.replace(/'/gi, "\\'");
    var sourceId = zitObj.sourceId.replace(/'/gi, "\\'");
    try
    {
        if(main.ADMIN_LEVEL == 1)
        {
            displayUpdateZone = "";
        }
    }
    catch(ex){/*do nothing invalid user*/}
    var sleeping;
    if(zitObj.asleep == 1){
        sleeping = 'Sleeping';
    }
    else{
        sleeping = 'Awake';
    }
	
    var html = '<table style="font-family:\'Courier New\', Courier, monospace;width:600px;padding:5px;">'+
    '<tr><td colspan="2" style="font-size:18px;font-weight:bold;color:#666666;">ZIS Details</td></tr>'+
    '<tr><td style="font-weight:bold;">Description:</td><td>'+zitObj.zitName+'</td></tr>'+
    '<tr><td style="font-weight:bold;">Source Id:</td><td>'+zitObj.sourceId+'</td></tr>'+
    '<tr><td style="font-weight:bold;">ZIS Url:</td><td>'+zitObj.zitUrl+'</td></tr>'+
    '<tr><td style="font-weight:bold;">Admin Url:</td><td>'+zitObj.adminUrl+'</td></tr>'+
    '<tr><td style="font-weight:bold;">Min Buffer:</td><td>'+zitObj.minBuffer+'</td></tr>'+
    '<tr><td style="font-weight:bold;">Max Buffer:</td><td>'+zitObj.maxBuffer+'</td></tr>'+
    '<tr><td style="font-weight:bold;">Status:</td><td>'+sleeping;
	
    try{
        if(main.ADMIN_LEVEL == 1){
            html += '&nbsp;<a href="javascript:zit.putZitToSleep('+zitObj.asleep+');">Change</a>';
        }
    }
    catch(ex){
    //do nothing invalid user
    }
				   
				   
    html += '</td></tr><tr><td style="font-weight:bold;">Supported Versions:</td><td>';
    var length = zones.versions.length;
    for(var i = 0; i < length; i++){
        html += '['+zones.versions[i].desc+']';
    }
    html += '</td></tr>'+
    '<tr><td colspan="2"><input type="button" style="display:'+displayUpdateZone+';" value="Update" class="button" onclick="zit.showUpdateZit(\''+zitName+'\', \''+sourceId+'\', \''+zitObj.adminUrl+'\', \''+zitObj.zitUrl+'\', \''+zitObj.minBuffer+'\', \''+zitObj.maxBuffer+'\')" /></td></tr>'+
    '</table>';

    main.clearTabs();
    zit.errorMessages = obj.errorMessages;
    main.addTab_item(zit.buildErrorMessageGrid());
    main.addTab(html,'ZIS Details');
};

zit.getZitInformation = function(){
    main.clearTabs();
    main.addTab_item(main.insertLoadingPanel());
	
    Ext.Ajax.request({
        url: './zit',
        method:'post',
        params: {},
        success:function(response){
            obj = Ext.util.JSON.decode(response.responseText);
            zit.buildZitInformation(obj);
        },
        failure:function(response){
            Ext.Msg.alert('Error!','Error Getting ZIS Information');
        }
    });
};

zit.archiveErrorMessages = function(){
    Ext.MessageBox.alert('Loading....', "Archiving Error Messages....");

    var elem = tabPanel.getActiveTab();
	
    Ext.Ajax.request({
        url: './zit/archivemessages',
        method:'post',
        params: {lic:main.LIC},
        success:function(response){
            obj = Ext.util.JSON.decode(response.responseText);
            if(obj.success)
            {
                zit.errorMessages = new Array();
                var grid = zit.buildErrorMessageGrid();
                tabPanel.remove(elem);
                tabPanel.insert(0, grid).show();
                Ext.MessageBox.alert('Success', "Messages Archived");
            }
            else
            {
                Ext.Msg.alert('Error!','Error Archiving Messages');
            }
        },
        failure:function(response){
            Ext.Msg.alert('Error!','Error Archiving Messages');
        }
    });
}

zit.refreshErrorMessages = function(){
    Ext.MessageBox.alert('Loading....', "Refreshing Error Messages....");

    var elem = tabPanel.getActiveTab();
	
    Ext.Ajax.request({
        url: './zit/geterrormessages',
        method:'post',
        params: {lic:main.LIC},
        success:function(response){
            obj = Ext.util.JSON.decode(response.responseText);
            if(obj.success)
            {
                zit.errorMessages = obj.errorMessages;
                var grid = zit.buildErrorMessageGrid();
                tabPanel.remove(elem);
                tabPanel.insert(0, grid).show();
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

zit.convertNullToNa = function(value){
    if(value == null){
        return "N/A";
    }
    else{
        return value;
    }
}