// agent Javascript File

var agent = {};

agent.pushMessages;
agent.receivedMessages;
agent.AGENT_ID;

agent.getAgentDetails = function(zoneId, agentId){
    main.clearTabs();
    main.addTab_item(main.insertLoadingPanel());
  
    Ext.Ajax.request({
        url: './agent/getagent',
        method:'post',
        params: {
            AGENT_ID:agentId,
            ZONE_ID:zoneId,
            lic:main.LIC
        },
        success:function(response){
      myCode = response.responseText;
      if (myCode.substr(0,2) == "/*") {
      myCode = myCode.substring(2, myCode.length - 2);
      }
            obj = Ext.util.JSON.decode(myCode);
            if(obj.success)
            {
                agent.buildAgentSection_HTML(obj, zoneId);
            }
            else
            {
                Ext.Msg.alert('Error!','Error Getting Agent Details');
            }
        },
        failure:function(response){
            Ext.Msg.alert('Error!','Error Getting Agent Details');
        }
    });
};

agent.checkContactInfo = function(str){
    if(str == "" || str == ' '){
        return 'N/A';
    }
    else{
        return str;
    }
};

agent.providePublishHelper = function(val){
    if(val == 1){
        return 'Yes';
    }
    else{
        return 'No';
    }
};

agent.buildProvidedObjectsGrid = function(providedObjects){
    var length = providedObjects.length;
    var data = '[';
    for(var i = 0; i < length; i++){
    
        data += "['"+providedObjects[i].name+"',";
        data += "'"+providedObjects[i].timestamp+"',";
        data += "'"+agent.providePublishHelper(providedObjects[i].add)+"',";
        data += "'"+agent.providePublishHelper(providedObjects[i].update)+"',";
        data += "'"+agent.providePublishHelper(providedObjects[i].delete_)+"'";
        if(i == (length - 1)){
            data += "]";
        }
        else{
            data += "],";
        }
    }
    data += ']';
  
    var dataObj = eval(data);
  
    return grids.createAgentProvisionsGrid(dataObj);
};

agent.buildSubscribedObjectsGrid = function(subscribedObjects){
    var length = subscribedObjects.length;
    var data = '[';
    for(var i = 0; i < length; i++){
    
        data += "['"+subscribedObjects[i].name+"',";
        data += "'"+subscribedObjects[i].timestamp+"'";
        if(i == (length - 1)){
            data += "]";
        }
        else{
            data += "],";
        }
    }
    data += ']';
  
    var dataObj = eval(data);
  
    return grids.createAgentSubscriptionsGrid(dataObj);
};

agent.buildPushMessageListGrid = function(messages)
{
    var dataObj;
  
    var length = messages.length;
    var data = '[';
    for(var i = 0; i < length; i++){
    
        data += "['"+messages[i].timestamp+"',";
        data += "'"+messages[i].messageType+"',";
        data += '\'<a href="javascript:agent.showPushXmlMessage('+i+', 2);">View</a>\',';
        data += '\'<a href="javascript:agent.showPushXmlMessage('+i+', 1);">View</a>\'';
        if(i == (length - 1)){
            data += "]";
        }
        else{
            data += "],";
        }
    }
    data += ']';
  
    dataObj = eval(data);
  
    return grids.createAgentPushMessageGrid(dataObj);
};

agent.buildReceivedMessageListGrid = function(messages)
{
    var dataObj;
  
    var length = messages.length;
    var data = '[';
    for(var i = 0; i < length; i++){
    
        data += "['"+messages[i].timestamp+"',";
        data += "'"+messages[i].messageType+"',";
        data += '\'<a href="javascript:agent.showReceivedXmlMessage('+messages[i].id+',1);">View</a>\',';
        data += '\'<a href="javascript:agent.showReceivedXmlMessage('+messages[i].id+',2);">View</a>\'';
        if(i == (length - 1)){
            data += "]";
        }
        else{
            data += "],";
        }
    }
    data += ']';
  
    dataObj = eval(data);
  
    return grids.createAgentReceivedMessageGrid(dataObj);
};

agent.showPushXmlMessage = function(index, type)
{
    /*
   type
   1 = rec
   2 = sent
  */
    var html;
    if(type == 1)
    {
        html = "<div align='center'><textarea style='overflow:auto;' cols='70' rows='18'>"+agent.pushMessages[index].recMessage+"</textarea></div>";
        main.showWindow_noBtn('Recieved Message', html, 360, 595);
    }
    else
    {
        html = "<div align='center'><textarea style='overflow:auto;' cols='70' rows='18'>"+agent.pushMessages[index].sentMessage+"</textarea></div>";
        main.showWindow_noBtn('Sent Message', html, 360, 595);
    }
}

agent.showReceivedXmlMessage = function(index, type)
{
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
/*
    var html;
    if(type == 1)
    {
        html = "<div align='center'><textarea style='overflow:auto;' cols='70' rows='18'>"+agent.receivedMessages[index].recMessage+"</textarea></div>";
        main.showWindow_noBtn('Recieved Message', html, 360, 595);
    }
    else
    {
        html = "<div align='center'><textarea style='overflow:auto;' cols='70' rows='18'>"+agent.receivedMessages[index].sentMessage+"</textarea></div>";
        main.showWindow_noBtn('Sent Message', html, 360, 595);
    }
*/
};

agent.buildAgentSection_HTML = function(obj, zoneId)
{
    agent.AGENT_ID = obj.agents.agentId;
    var agentObj = obj.agents;
  
    var agentDetailHtml = '<table style="font-family:\'Courier New\', Courier, monospace;padding:10px;">'+
    '<tr><td colspan="2" style="font-size:18px;font-weight:bold;color:#666666;">Agent Details</td></tr>'+
    '<tr><td style="font-weight:bold;">Source ID</td><td align="left">'+agentObj.sourceId+'</td></tr>'+
    '<tr><td style="font-weight:bold;">Description</td><td align="left">'+agentObj.agentDesc+'</td></tr>'+
    '<tr><td style="font-weight:bold;">Certificate DN</td><td align="left">'+agentObj.certCommonDn+'</td></tr>'+
    '<tr><td style="font-weight:bold;">Registration Type</td><td align="left">'+agentObj.status+'</td></tr>'+
    '<tr><td style="font-weight:bold;">Call Back Url</td><td align="left">'+main.urlDecode(agentObj.agentCallbackUrl)+'</td></tr>'+
    '<tr><td style="font-weight:bold;">Awake</td><td align="left">'+agent.AwakeConvertor(agentObj.sleeping)+'</td></tr>'+
    '<tr><td style="font-weight:bold;">Authentication Level&nbsp;</td><td align="left">'+agentObj.authenticationLevel+'</td></tr>'+
    '<tr><td colspan="2"><hr/></td></tr>'+
//    '<tr><td colspan="2" style="font-size:18px;font-weight:bold;color:#666666;">Contact Information</td></tr>'+
//    '<tr><td style="font-weight:bold;">Name</td><td align="left">'+agent.checkContactInfo(agentObj.contactName)+'</td></tr>'+
//    '<tr><td style="font-weight:bold;">Email</td><td align="left">'+agent.checkContactInfo(agentObj.contactEmail)+'</td></tr>'+
//    '<tr><td style="font-weight:bold;">Phone</td><td align="left">'+agent.checkContactInfo(agentObj.contactPhone)+'</td></tr>'+
//    '<tr><td style="font-weight:bold;">Address</td><td align="left">'+agent.checkContactInfo(agentObj.contactAddress)+'</td></tr>'+
    '</table>';
  
    agent.receivedMessages = obj.agents.receivedMessages;
    agent.pushMessages = obj.agents.pushMessages;
  
    tabPanel.removeAll(true);
    main.addTab(agentDetailHtml,'Agent Details');
    main.addTab_item(agent.buildReceivedMessageListGrid(agent.receivedMessages));
    main.addTab_item(agent.buildPushMessageListGrid(agent.pushMessages));
    main.addTab_item(agent.buildProvidedObjectsGrid(obj.agents.providing));
    main.addTab_item(agent.buildSubscribedObjectsGrid(obj.agents.subscribing));
    main.addTab_item(access.buildCurrentAgentPermissions(obj.permissions, zoneId, agentObj.agentId));
    main.addTab_item(openZis.elementFiltering.buildElementFilterTree(agent.AGENT_ID, zoneId, 1));
    tabPanel.setActiveTab(0)
};

agent.AwakeConvertor = function(sleeping)
{
    var awake = "";
    if(sleeping != null && sleeping != ''){
        if(sleeping == "0"){
            awake = "Yes";
        }
        else{
            awake = "No";
        }
    }
    return awake;
}

agent.refreshAgentReceivedMessages = function()
{
    Ext.MessageBox.alert('Loading....', "Refreshing Messages....");

    var elem = tabPanel.getActiveTab();
  
    Ext.Ajax.request({
        url: './agent/getagentmessages',
        method:'post',
        params: {
            AGENT_ID:agent.AGENT_ID,
            ZONE_ID:zones.ZONE_ID,
            ZIT_LOG_MESSAGE_TYPE:2,
      lic:main.LIC
        },
        success:function(response){
      myCode = response.responseText;
      if (myCode.substr(0,2) == "/*") {
      myCode = myCode.substring(2, myCode.length - 2);
      }
            obj = Ext.util.JSON.decode(myCode);
            if(obj.success)
            {
        
                agent.receivedMessages = obj.receivedMessages;
                var grid = agent.buildReceivedMessageListGrid(agent.receivedMessages);
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

agent.refreshAgentPushMessages = function()
{
    Ext.MessageBox.alert('Loading....', "Refreshing Messages....");

    var elem = tabPanel.getActiveTab();
  
    Ext.Ajax.request({
        url: './agent/getagentmessages',
        method:'post',
        params: {
            AGENT_ID:agent.AGENT_ID,
            ZONE_ID:zones.ZONE_ID,
            ZIT_LOG_MESSAGE_TYPE:1,
      lic:main.LIC
        },
        success:function(response){
            myCode = response.responseText;
      if (myCode.substr(0,2) == "/*") {
      myCode = myCode.substring(2, myCode.length - 2);
      }
            obj = Ext.util.JSON.decode(myCode);
            if(obj.success)
            {
        
                agent.pushMessages = obj.pushMessages;
                var grid = agent.buildPushMessageListGrid(agent.pushMessages);
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

agent.buildAgentMessageGrid = function()
{
    main.clearTabs();
  main.addTab_item(agent.buildPushMessageListGrid(agent.pushMessages));
    main.addTab_item(agent.buildReceivedMessageListGrid(agent.receivedMessages));

};

agent.getAgentMessages = function(agentId)
{
    agent.AGENT_ID = agentId;
    main.clearTabs();
    main.addTab_item(main.insertLoadingPanel());
  
    Ext.Ajax.request({
        url: './agent/getagentmessages',
        method:'post',
        params: {
            AGENT_ID:agentId,
      lic:main.LIC
        },
        success:function(response){
            myCode = response.responseText;
      if (myCode.substr(0,2) == "/*") {
      myCode = myCode.substring(2, myCode.length - 2);
      }
            obj = Ext.util.JSON.decode(myCode);
            if(obj.success)
            {
                agent.pushMessages = obj.pushMessages;
                agent.receivedMessages = obj.receivedMessages;
                agent.buildAgentMessageGrid();
            }
            else
            {
                Ext.Msg.alert('Error!','Error Getting Agents Messages');
            }
        },
        failure:function(response){
            Ext.Msg.alert('Error!','Error Getting Agents Messages');
        }
    });
};

agent.getAgentList = function()
{
    try{
        agent_panel.remove(agent_panel.getComponent(0))
        }catch(ex){/*do nothing*/};
    agent_panel.add(main.insertLoadingPanel());
    viewport.doLayout();
  
    Ext.Ajax.request({
        url: './agent/getagentlist',
        method:'post',
        params: {
      lic:main.LIC
      },
        success:function(response){
            myCode = response.responseText;
      if (myCode.substr(0,2) == "/*") {
      myCode = myCode.substring(2, myCode.length - 2);
      }
            obj = Ext.util.JSON.decode(myCode);
            if(obj.success)
            {
                agent.buildAgentList(obj.agents);
            }
            else
            {
                Ext.Msg.alert('Error!','Error Getting Agents');
            }
        },
        failure:function(response){
            Ext.Msg.alert('Error!','Error Getting Agents');
        }
    });
};

agent.buildAgentList = function(agents){
    var length = agents.length;
    var data = '[';
    var desc;
    var sourceId;
    for(var i = 0; i < length; i++){
        desc         = agents[i].agentDesc.replace(/'/gi, "\\'");
        sourceId     = agents[i].sourceId.replace(/'/gi, "\\'");
        username       = agents[i].username.replace(/'/gi, "\\'");
        ipaddress    = agents[i].ipaddress.replace(/'/gi, "\\'");
        maxbuffer    = agents[i].maxbuffersize.replace(/'/gi, "\\'");
        certCommon   = agents[i].certCommonDn.replace(/'/gi, "\\'");
    
        data += "['<a href=\"javascript:agent.showEditAgent(\\'"+agents[i].agentId+"\\', \\'"+sourceId+"\\', \\'"+desc+"\\', \\'"+agents[i].username+"\\', \\'"+agents[i].password+"\\', "+agents[i].active+"\\, \\'"+ipaddress+"\\', \\'"+maxbuffer+"\\', \\'"+certCommon+"\\')\">"+sourceId+"</a>',";
  
  
        data += "'"+username+"',";
        data += "'<a href=\"javascript:agent.getAgentMessages("+agents[i].agentId+")\">Messages</a>'";
        if(i == (length - 1)){
            data += "]";
        }
        else{
            data += "],";
        }
    }
    data += ']';
  
    var dataObj = eval(data);
  
    try{
        agent_panel.remove(agent_panel.getComponent(0))
        }catch(ex){/*do nothing*/}
    agent_panel.add(grids.createAgentListGrid(dataObj));
    viewport.doLayout();
};

agent.showAddAgent = function(){

    var addAgentForm = new Ext.FormPanel({ 
        labelWidth:90,
        url:'./agent/addagent',
        frame:true,
        width:450,
        defaultType:'textfield',
        monitorValid:true,
        items:[{
            fieldLabel:'Source Id',
            name:'SOURCE_ID',
      		emptyText: 'ex: moodle.lake.k12.fl.us',
      		regex: /^[a-zA-Z0-9_.]+$/,
      		regexText : 'Alphanumeric, underscore, and periods allowed',
            allowBlank:false,
      		width:300
        },{
          fieldLabel:'Description',
          name:'DESCRIPTION',
      emptyText: 'ex: moodle.lake.k12.fl.us',
      regex: /^[a-zA-Z0-9_. ]+$/,
      regexText : 'Alphanumeric, underscore, periods, and spaces allowed',
          allowBlank:false,
      width:300
      },{
      xtype:'fieldset',
          columnWidth: 0.5,
          title: 'Http Authentication',
          collapsible: false,
          autoHeight:true,
          defaults: {
              anchor: '-20'
          },
          defaultType: 'textfield',
          items :[{
                  fieldLabel:'Username',
                  name:'USERNAME',
                  allowBlank:true
              },{
                  fieldLabel:'Password',
                  name:'PASSWORD',
                  inputType:'password',
                  allowBlank:true
              }
    ]},{
      xtype:'fieldset',
          columnWidth: 0.5,
          title: 'Fixed IP Address',
          collapsible: false,
          autoHeight:true,
          defaults: {
              anchor: '-20'
          },
          defaultType: 'textfield',
          items :[{
                  fieldLabel:'IP Address',
                  name:'IPADDRESS',
            	  emptyText: 'ex: 58.37.12.42',
            	  regex: /^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/,
            	  maskRe: /[0-9.]/,
                  allowBlank:true
      }
    ]}, {
      xtype:'fieldset',
          columnWidth: 0.5,
          title: 'RESTful Agent',
          collapsible: false,
          autoHeight:true,
          defaults: {
              anchor: '-20'
          },
          defaultType: 'textfield',
          items :[{
                fieldLabel:'Max BufferSize',
                name:'MAXBUFFERSIZE',
            	emptyText: 'ex: 1024000',
            	regex: /^(\d{1,8})$/,
            	maskRe: /[0-9]/,
                allowBlank:true
        }
      ]},{
          xtype:'hidden',
      name:'lic'
      }],
        buttons:[{
            text:'Save',
            formBind: true,
            handler:function(){
        addAgentForm.getForm().findField('lic').setValue(main.LIC);
                addAgentForm.getForm().submit({
                    method:'POST',
					submitEmptyText: false,
                    waitTitle:'Connecting',
                    waitMsg:'Sending data...',
                    success:function(form, action)
                    {
                        myCode = action.response.responseText;
            if (myCode.substr(0,2) == "/*") {
            myCode = myCode.substring(2, myCode.length - 2);
            }
                  obj = Ext.util.JSON.decode(myCode);
                        if(obj.success)
                        {
                            agent.getAgentList();
                            Ext.Msg.alert('Success', "Agent Created");
                            win.destroy();
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
        width:450,
        height:400,
        closable: false,
        resizable: false,
        plain: true,
        modal: true,
        title: 'Add Agent',
        items: [addAgentForm]
    });
    win.show();

};

agent.showEditAgent = function(agentId, sourceId, agentDesc, username, password, active, ipAddress, maxbuffersize, certCommon){
    var updateAgentForm = new Ext.FormPanel({
        labelWidth:90,
        url:'./agent/updateAgent',
        frame:true,
        width:350,
        defaultType:'textfield',
        monitorValid:true,
        items:[new Ext.form.ComboBox({
            fieldLabel:'Status',
            width: 100,
            store: new Ext.data.SimpleStore({
                fields: ['statusValue', 'statusDescription'],
                data : [['1','Active'],['0','Inactive']]
            }),
            typeAhead: false,
            displayField: 'statusDescription',
            valueField: 'statusValue',
            forceSelection:true,
            editable:false,
            mode: 'local',
            selectOnFocus:true,
            value: active,
            emptyText: 'Status...',
            hiddenName: 'ACTIVE',
            triggerAction: 'all'
        }),new Ext.form.Hidden({
            name: 'ID',
            value: agentId
        }), new Ext.form.Hidden({
              name: 'lic'
      }),{
            fieldLabel:'Source Id',
            name:'SOURCE_ID',
      		emptyText: 'ex: moodle.lake.k12.fl.us',
      		regex: /^[a-zA-Z0-9_.]+$/,
      		regexText : 'Alphanumeric, underscore, and periods allowed',
      		value: sourceId,
            allowBlank:false,
      		width:350
        },{
          fieldLabel:'Description',
          	name:'DESCRIPTION',
      		emptyText: 'ex: moodle.lake.k12.fl.us',
      		regex: /^[a-zA-Z0-9_. ]+$/,
      		regexText : 'Alphanumeric, underscore, periods, and spaces allowed',
      		value: agentDesc,
          	allowBlank:false,
      		width:350
        },{
      xtype:'fieldset',
          columnWidth: 0.5,
          title: 'Certificate Authentication',
          collapsible: false,
          autoHeight:true,
          defaults: {
              anchor: '-20'
          },
          defaultType: 'textarea',
          items :[{
                  xtype: 'displayfield',
				  fieldLabel: 'Message text',
				  hideLabel: true,
				  name: 'CERT',
				  value: certCommon,
				  readOnly:true
              }
      ]}		,{
	      xtype:'fieldset',
	          columnWidth: 0.5,
	          title: 'Http Authentication',
	          collapsible: false,
	          autoHeight:true,
	          defaults: {
	              anchor: '-20'
	          },
	          defaultType: 'textfield',
	          items :[{
	                  fieldLabel:'Username',
	                  name:'USERNAME',
	            	  value: username,
	                  allowBlank:true
	              },{
	                  fieldLabel:'Password',
	                  name:'PASSWORD',
	                  inputType:'password',
	                  value: password,
	                  allowBlank:true
	              }
	      ]},{
      xtype:'fieldset',
          columnWidth: 0.5,
          title: 'Fixed IP Address',
          collapsible: false,
          autoHeight:true,
          defaults: {
              anchor: '-20'
          },
          defaultType: 'textfield',
          items :[{
           	fieldLabel:'IP Address',
            name:'IPADDRESS',
            emptyText: 'ex: 192.168.1.1',
            regex: /^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/,
            maskRe: /[0-9.]/,
                  allowBlank:true,
            value:ipAddress
      }
    ]}],
        buttons:[{
            text: ' Remove Certificate ',
            formBind: false,
            handler:function(){
                win.destroy();
            }
        },{
            text:'Update',
            formBind: true,
            handler:function(){
        		updateAgentForm.getForm().findField('lic').setValue(main.LIC);
                updateAgentForm.getForm().submit({
                    url:'./agent/updateAgent',
					method:'POST',
					submitEmptyText: false,
                    waitTitle:'Connecting',
                    waitMsg:'Sending data...',
                    success:function(f, a)
                    {
                        Ext.Msg.alert('Success', "Update");
            myCode = a.response.responseText;
            if (myCode.substr(0,2) == "/*") {
            myCode = myCode.substring(2, myCode.length - 2);
            }
                  obj = Ext.util.JSON.decode(myCode);
                        if(obj.success)
                        {
                            agent.getAgentList();
                            Ext.Msg.alert('Success', "Agent Updated");
                            win.destroy();
                        }
                        else
                        {
                            Ext.Msg.alert('Error!','Error Updating Agent');
                        }
                    },
                    failure:function(f, a)
                    {
                        if(action.failureType == 'server')
                        {
                            myCode = a.response.responseText;
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
        },{
            text: 'Delete',
            formBind: false,
            handler:function(){
                Ext.MessageBox.confirm('Confirm', 'Are you sure you want to do delete this agent?', function(btn){
                    if(btn == 'yes')
                    {
                        agent.deleteAgent(agentId);
                    }
                    else
                    {
                        return;
                    }
                });
            }
        }]
    });
   
    if(win){
        win.destroy();
    }
    win = new Ext.Window({
        layout:'fit',
        width:500,
        height:500,
        closable: false,
        resizable: true,
        plain: true,
        modal: true,
        title: 'Update Agent',
        items: [updateAgentForm]
    });
    win.show();
};

agent.deleteAgent = function(agentId){
    Ext.Ajax.request({
        url: './agent/deleteAgent',
        method:'post',
        params: {
            AGENT_ID:agentId,
      lic:main.LIC
        },
        success:function(response){
      myCode = response.responseText;
      if (myCode.substr(0,2) == "/*") {
      myCode = myCode.substring(2, myCode.length - 2);
      }
            obj = Ext.util.JSON.decode(myCode);
            if(obj.success)
            {
                agent.getAgentList();
                Ext.MessageBox.alert('Success', "Agent Deleted");
                win.destroy();
            }
            else
            {
                Ext.Msg.alert('Error!','Error Deleting Agent');
            }
        },
        failure:function(response){
            Ext.Msg.alert('Error!','Error Deleting Agent');
        }
    });
};