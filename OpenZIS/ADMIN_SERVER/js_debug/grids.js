// grids.js

grids = {};

grids.createErrorMessagesGrid = function(data){
    var store = new Ext.data.SimpleStore({
        fields: 
        [
        {
            name: 'timestamp'
        },

        {
            name: 'location'
        },

        {
            name: 'shortDescription'
        },

        {
            name: 'zone'
        },

        {
            name: 'agent'
        },

        {
            name: 'moreInfo'
        }
        ]
    });
    store.loadData(data);
	
    var grid = new Ext.grid.GridPanel({
        store: store,
        columns: 
        [
        {
            header: "Timestamp",
            width: 134,
            sortable: true,
            dataIndex:'timestamp'
        },

        {
            header: "Location",
            width: 210,
            sortable: true,
            dataIndex:'location'
        },

        {
            header: "Short Description",
            width: 210,
            sortable: true,
            dataIndex:'shortDescription'
        },

        {
            header: "Zone",
            width: 153,
            sortable: true,
            dataIndex:'zone'
        },

        {
            header: "Agent",
            width: 153,
            sortable: true,
            dataIndex:'agent'
        },

        {
            header: "More Info",
            width: 153,
            sortable: true,
            dataIndex:'moreInfo'
        }
        ],
        stripeRows: true,
        autoHeight:false,
        height:760,
        layout:'fit',
        title:'Error Messages',
        buttonAlign:'left',
        buttons: [
        {
            text: 'Refresh',
            handler: function(){
                zit.refreshErrorMessages();
            }
        },
        {
            text: 'Archive Messages',
            handler: function(){
                zit.archiveErrorMessages();
            }
        }
        ]
    });
    return grid;
};

grids.createAdminListGrid = function(data){
    var store = new Ext.data.SimpleStore({
        fields: 
        [
        {
            name: 'username'
        },

        {
            name: 'lastlogin'
        },

        {
            name: 'active'
        }
        ]
    });
    store.loadData(data);
	
    var grid = new Ext.grid.GridPanel({
        store: store,
        columns: 
        [
        {
            header: "Username",
            width: 157,
            sortable: true,
            dataIndex:'username'
        },

        {
            header: "Last Login",
            width: 135,
            sortable: true,
            dataIndex:'lastlogin'
        },

        {
            header: "Active",
            width: 55,
            sortable: true,
            dataIndex:'active'
        }
        ],
        stripeRows: true,
        autoHeight:false,
        height:420,
        layout:'fit',
        title:''
    });
    return grid;
};

grids.createGroupPermissionItemsGrid = function(data){
	
    var store = new Ext.data.SimpleStore({
        fields: 
        [
        {
            name: 'object'
        },

        {
            name: 'provide'
        },

        {
            name: 'subscribe'
        },

        {
            name: 'request'
        },

        {
            name: 'respond'
        },

        {
            name: 'add'
        },

        {
            name: 'update'
        },

        {
            name: 'delete'
        },

        {
            name: 'update_link'
        }
        ]
    });
    store.loadData(data);
	
    var grid = new Ext.grid.GridPanel({
        store: store,
        columns: [
        {
            header: "Object",
            dataIndex: 'object',
            sortable: true,
            width: 200
        },

        {
            header: "Provide",
            width: 100,
            align:'center'
        },

        {
            header: "Subscribe",
            width: 100,
            align:'center'
        },

        {
            header: "Request",
            width: 100,
            align:'center'
        },

        {
            header: "Respond",
            width: 100,
            align:'center'
        },

        {
            header: "Publish Add",
            width: 100,
            align:'center'
        },

        {
            header: "Publish Update",
            width: 100,
            align:'center'
        },

        {
            header: "Publish Delete",
            width: 100,
            align:'center'
        },

        {
            header: "Update",
            width: 75,
            align:'center'
        }
        ],
        stripeRows: true,
        autoHeight:false,
        layout:'fit',
        height:400,
        title:'Group Permission Items',
        buttonAlign:'left',
        buttons: [
        {
            text: 'Add Permission',
            handler: function(){
                groupPermission.showAddPermission();
            }
        }
        ]
    });  
	
    return grid;
};

grids.createPermssionGroupListGrid = function(data){
    var store = new Ext.data.SimpleStore({
        fields: 
        [
        {
            name: 'name'
        },

        {
            name: 'version'
        },

        {
            name: 'permissions'
        },

        {
            name: 'delete'
        }
        ]
    });
    store.loadData(data);
	
    var grid = new Ext.grid.GridPanel({
        store: store,
        columns: 
        [
        {
            header: "Name",
            width: 150,
            sortable: true,
            dataIndex:'name'
        },

        {
            header: "Version",
            width: 55,
            sortable: true,
            dataIndex:'version'
        },

        {
            header: "Permissions",
            width: 85,
            sortable: false,
            dataIndex:'permissions'
        },

        {
            header: "Delete",
            width: 55,
            sortable: false,
            dataIndex:'delete'
        }
        ],
        stripeRows: true,
        autoHeight:false,
        layout:'fit',
        height:475,
        title:''
    });
    return grid;
};

grids.createNewGroupPermissionItemGrid = function(data){
	
    var store = new Ext.data.SimpleStore({
        fields: 
        [
        {
            name: 'provide'
        },

        {
            name: 'subscribe'
        },

        {
            name: 'request'
        },

        {
            name: 'respond'
        },

        {
            name: 'add'
        },

        {
            name: 'update'
        },

        {
            name: 'delete'
        }
        ]
    });
    store.loadData(data);
	
    var grid = new Ext.grid.GridPanel({
        store: store,
        columns: [
        {
            header: "Provide",
            width: 100,
            align:'center'
        },

        {
            header: "Subscribe",
            width: 100,
            align:'center'
        },

        {
            header: "Request",
            width: 100,
            align:'center'
        },

        {
            header: "Respond",
            width: 100,
            align:'center'
        },

        {
            header: "Publish Add",
            width: 100,
            align:'center'
        },

        {
            header: "Publish Update",
            width: 100,
            align:'center'
        },

        {
            header: "Publish Delete",
            width: 100,
            align:'center'
        }
        ],
        stripeRows: true,
        autoHeight:false,
        height:175,
        title:'',
        layout:'fit',
        region:'center',
        buttonAlign:'center',
        buttons: [
        {
            text: 'Save Permission',
            handler: function(){
                groupPermission.saveNewGroupItem();
            }
        }
        ]
    });  
	
    return grid;
};

grids.createNewAccessControlGrid = function(data){
	
    var store = new Ext.data.SimpleStore({
        fields: 
        [
        {
            name: 'provide'
        },

        {
            name: 'subscribe'
        },

        {
            name: 'request'
        },

        {
            name: 'respond'
        },

        {
            name: 'add'
        },

        {
            name: 'update'
        },

        {
            name: 'delete'
        }
        ]
    });
    store.loadData(data);
	
    var grid = new Ext.grid.GridPanel({
        store: store,
        columns: [
        {
            header: "Provide",
            width: 100,
            align:'center'
        },

        {
            header: "Subscribe",
            width: 100,
            align:'center'
        },

        {
            header: "Request",
            width: 100,
            align:'center'
        },

        {
            header: "Respond",
            width: 100,
            align:'center'
        },

        {
            header: "Publish Add",
            width: 100,
            align:'center'
        },

        {
            header: "Publish Update",
            width: 100,
            align:'center'
        },

        {
            header: "Publish Delete",
            width: 100,
            align:'center'
        }
        ],
        stripeRows: true,
        autoHeight:false,
        width:700,
        height:200,
        title:'',
        region:'center',
        buttonAlign:'center',
        buttons: [
        {
            text: 'Save Permission',
            handler: function(){
                access.saveNewPermission();
            }
        }
        ]
    });  
	
    return grid;
};

grids.createAccessControlGrid = function(data){
	
    var store = new Ext.data.SimpleStore({
        fields: 
        [
        {
            name: 'object'
        },

        {
            name: 'provide'
        },

        {
            name: 'subscribe'
        },

        {
            name: 'request'
        },

        {
            name: 'respond'
        },

        {
            name: 'add'
        },

        {
            name: 'update'
        },

        {
            name: 'delete'
        },

        {
            name: 'update_link'
        }
        ]
    });
    store.loadData(data);
	
    var grid = new Ext.grid.GridPanel({
        store: store,
        columns: [
        {
            header: "Object",
            dataIndex: 'object',
            sortable: true,
            width: 200
        },

        {
            header: "Provide",
            width: 100,
            align:'center'
        },

        {
            header: "Subscribe",
            width: 100,
            align:'center'
        },

        {
            header: "Request",
            width: 100,
            align:'center'
        },

        {
            header: "Respond",
            width: 100,
            align:'center'
        },

        {
            header: "Publish Add",
            width: 100,
            align:'center'
        },

        {
            header: "Publish Update",
            width: 100,
            align:'center'
        },

        {
            header: "Publish Delete",
            width: 100,
            align:'center'
        },

        {
            header: "Update",
            width: 75,
            align:'center'
        }
        ],
        stripeRows: true,
        autoHeight:false,
        width:975,
        height:400,
        title:'Access Controls',
        buttonAlign:'left',
        buttons: [
        {
            text: 'Add Permission',
            handler: function(){
                access.showAddPermission();
            }
        },
        {
            text: 'Use Permission Group',
            handler: function(){
                access.showAddPermissionGroup();
            }
        }
        ]
    });  
	
    return grid;
};

grids.createAgentSubscriptionsGrid = function(data){
    var store = new Ext.data.SimpleStore({
        fields: 
        [
        {
            name: 'dataObj'
        },

        {
            name: 'provisionDate'
        }
        ]
    });
    store.loadData(data);
	
    var grid = new Ext.grid.GridPanel({
        store: store,
        columns: 
        [
        {
            header: "Data Object",
            width: 350,
            sortable: true,
            dataIndex:'dataObj'
        },

        {
            header: "Subscription Date",
            width: 340,
            sortable: true,
            dataIndex:'provisionDate'
        }
        ],
        stripeRows: true,
        autoHeight:false,
        width:690,
        height:400,
        title:'Subscribed Objects'
    });
    return grid;
};

grids.createAgentProvisionsGrid = function(data){
    var store = new Ext.data.SimpleStore({
        fields: 
        [
        {
            name: 'dataObj'
        },

        {
            name: 'provisionDate'
        },

        {
            name: 'add'
        },

        {
            name: 'change'
        },

        {
            name: 'delete'
        }
        ]
    });
    store.loadData(data);
	
    var grid = new Ext.grid.GridPanel({
        store: store,
        columns: 
        [
        {
            header: "Data Object",
            width: 200,
            sortable: true,
            dataIndex:'dataObj'
        },

        {
            header: "Provision Date",
            width: 140,
            sortable: true,
            dataIndex:'provisionDate'
        },

        {
            header: "Publish Add",
            width: 100,
            sortable: true,
            dataIndex:'add'
        },

        {
            header: "Publish Change",
            width: 100,
            sortable: true,
            dataIndex:'change'
        },

        {
            header: "Publish Delete",
            width: 100,
            sortable: true,
            dataIndex:'delete'
        }
        ],
        stripeRows: true,
        autoHeight:false,
        width:690,
        height:400,
        title:'Provided Objects'
    });
    return grid;
};

grids.createDataObjGrid = function(data){
    var store = new Ext.data.SimpleStore({
        fields: 
        [
        {
            name: 'name'
        }
        ]
    });
    store.loadData(data);
	
    var grid = new Ext.grid.GridPanel({
        store: store,
        columns: 
        [
        {
            header: "Name",
            width: 650,
            sortable: true,
            dataIndex:'name'
        }
        ],
        stripeRows: true,
        autoHeight:false,
        width:650,
        height:200,
        title:'Data Objects',
        buttonAlign:'left'
    /*buttons: [
				  {
					text: 'Add Data Object',
					handler: function(){dataObject.newDataObject();}
				  }
				 ]*/
    });
    return grid;
};

grids.createDataObjGroupGrid = function(data, versionId){
    var store = new Ext.data.SimpleStore({
        fields: 
        [
        {
            name: 'name'
        },

        {
            name: 'numObjs'
        }
        ]
    });
    store.loadData(data);
	
    var grid = new Ext.grid.GridPanel({
        store: store,
        columns: 
        [
        {
            header: "Name",
            width: 350,
            sortable: true,
            dataIndex:'name'
        },

        {
            header: "# Data Objects",
            width: 300,
            sortable: true,
            dataIndex:'numObjs'
        }
        ],
        stripeRows: true,
        width:650,
        height:300,
        title:'Data Object Groups',
        buttonAlign:'left'
    /*buttons: [
				  {
					text: 'Add Group',
					handler: function(){dataObject.showAddGroup(versionId);}
				  }
				 ]*/
    });
    return grid;
};

grids.createDataObjVersionsGrid = function(data){
    var store = new Ext.data.SimpleStore({
        fields: 
        [
        {
            name: 'version'
        },

        {
            name: 'numGroups'
        }
        ]
    });
    store.loadData(data);
	
    var grid = new Ext.grid.GridPanel({
        store: store,
        columns: 
        [
        {
            header: "Version",
            width: 350,
            sortable: true,
            dataIndex:'version'
        },

        {
            header: "# Data Object Groups",
            width: 300,
            sortable: true,
            dataIndex:'numGroups'
        }
        ],
        stripeRows: true,
        width:650,
        height:200,
        title:'Versions'
    });
    return grid;
};

grids.createAgentListGrid = function(data){
    var store = new Ext.data.SimpleStore({
        fields: 
        [
        {
            name: 'description'
        },

        {
            name: 'creator'
        },

        {
            name: 'messages'
        }
        ]
    });
    store.loadData(data);
	
    var grid = new Ext.grid.GridPanel({
        store: store,
        columns: 
        [
        {
            header: "Description",
            width: 140,
            sortable: true,
            dataIndex:'description'
        },

        {
            header: "Creator Username",
            width: 135,
            sortable: true,
            dataIndex:'creator'
        },

        {
            header: "Messages",
            width: 65,
            sortable: false,
            dataIndex:'messages'
        }
        ],
        stripeRows: true,
        autoHeight:false,
        height:500,
        layout:'fit',
        title:''
    });
    return grid;
};

grids.createZoneListGrid = function(data){
    var store = new Ext.data.SimpleStore({
        fields: [
        {
            name: 'sourceId'
        },

        {
            name: 'description'
        },

        {
            name: 'status'
        },

        {
            name: 'agents'
        },

        {
            name: 'numMsgs'
        },

        {
            name: 'archiveMsgs'
        }
        ]
    });
    store.loadData(data);
	
    var grid = new Ext.grid.GridPanel({
        store: store,
        columns: [
        {
            header: "Source ID",
            width: 200,
            sortable: true,
            dataIndex:'sourceId'
        },

        {
            header: "Description",
            width: 200,
            sortable: true,
            dataIndex:'description'
        },

        {
            header: "Status",
            width: 50,
            sortable: true,
            dataIndex:'status'
        },

        {
            header: "# Agents",
            width: 100,
            sortable: true,
            dataIndex:'agents'
        },

        {
            header: "# Messages",
            width: 100,
            sortable: true,
            dataIndex:'numMsgs'
        },

        {
            header: "Archive Messages",
            width: 100,
            sortable: true,
            dataIndex:'archiveMsgs'
        }
        ],
        stripeRows: true,
        autoHeight:false,
        width:650,
        height:400,
        title:'Zones'
    });

    return grid;
};

grids.createZoneAgentListGrid = function(data){
    var store = new Ext.data.SimpleStore({
        fields: [
        {
            name: 'sourceId'
        },

        {
            name: 'description'
        },

        {
            name: 'status'
        },

        {
            name: 'awake'
        },

        {
            name: 'numMsgs'
        },

        {
            name: 'delete'
        }
        ]
    });
    store.loadData(data);
	
    var grid = new Ext.grid.GridPanel({
        store: store,
        columns: [
        {
            header: "Source ID",
            width: 200,
            sortable: true,
            dataIndex:'sourceId'
        },

        {
            header: "Description",
            width: 200,
            sortable: true,
            dataIndex:'description'
        },

        {
            header: "Status",
            width: 100,
            sortable: true,
            dataIndex:'status'
        },

        {
            header: "Awake",
            width: 50,
            sortable: true,
            dataIndex:'awake'
        },

        {
            header: "# Messages",
            width: 80,
            sortable: true,
            dataIndex:'numMsgs'
        },

        {
            header: "Remove",
            width: 70,
            sortable: true,
            dataIndex:'delete'
        }
        ],
        stripeRows: true,
        autoHeight:false,
        width:650,
        height:400,
        title:'Agents',
        buttonAlign:'left',
        buttons: [
        {
            text: 'Add Agent',
            handler: function(){
                zones.showAddAgent();
            }
        }
        ]
    });

    return grid;
};

grids.createAgentPushMessageGrid = function(data, messageReceptionTypeId){
    var store = new Ext.data.SimpleStore({
        fields: [
        {
            name: 'timestamp'
        },

        {
            name: 'msgType'
        },

        {
            name: 'recMsg'
        },

        {
            name: 'resMsg'
        }
        ]
    });
    store.loadData(data);
	
    var grid = new Ext.grid.GridPanel({
        store: store,
        columns: [
        {
            header: "Log Timestamp",
            width: 200,
            sortable: true,
            dataIndex:'timestamp'
        },

        {
            header: "MessageType",
            width: 150,
            sortable: true,
            dataIndex:'msgType'
        },

        {
            header: "Sent Message",
            width: 150,
            sortable: true,
            dataIndex:'recMsg'
        },

        {
            header: "Response Message",
            width: 150,
            sortable: true,
            dataIndex:'resMsg'
        }
        ],
        stripeRows: true,
        autoHeight:false,
        width:500,
        height:400,
        autoScroll:true,
        id:'agentPushMessagesGrid',
        title:"Push Messages",
        buttonAlign:'left',
        buttons: [
        {
            text: 'Refresh',
            handler: function(){
                agent.refreshAgentPushMessages();
            }
        }
        ]
    });

    return grid;
};

grids.createAgentReceivedMessageGrid = function(data){
    var store = new Ext.data.SimpleStore({
        fields: [
        {
            name: 'timestamp'
        },

        {
            name: 'msgType'
        },

        {
            name: 'recMsg'
        },

        {
            name: 'sentMsg'
        }
        ]
    });
    store.loadData(data);

    var grid = new Ext.grid.GridPanel({
        store: store,
        trackMouseOver:false,
        disableSelection:true,
        columns: [
        {
            header: "Log Timestamp",
            width: 200,
            sortable: true,
            dataIndex:'timestamp'
        },

        {
            header: "MessageType",
            width: 150,
            sortable: true,
            dataIndex:'msgType'
        },

        {
            header: "Recieved Message",
            width: 150,
            sortable: false,
            dataIndex:'recMsg'
        },

        {
            header: "Response Message",
            width: 150,
            sortable: false,
            dataIndex:'sentMsg'
        }
        ],
        stripeRows: true,
        autoHeight:false,
        width:500,
        height:400,
        autoScroll:true,
        id:'agentPullMessagesGrid',
        title:"Received Messages",
//       buttonAlign:'left',
		bbar: new Ext.PagingToolbar({
			pageSize: 25,
			store: store,
			displayInfo: true,
			displayMsg: 'Displaying topics {0} - {1} of {2}',
			emptyMsg: "No topics to display"
		})
   });

    return grid;
};

grids.createZonePushedMessageGrid = function(data){
    var store = new Ext.data.SimpleStore({
        fields: [
        {
            name: 'timestamp'
        },

        {
            name: 'agent'
        },

        {
            name: 'msgType'
        },

        {
            name: 'sentMsg'
        },

        {
            name: 'recMsg'
        }
        ]
    });
    store.loadData(data);
	
    var grid = new Ext.grid.GridPanel({
        store: store,
        columns: [
        {
            header: "Log Timestamp",
            width: 150,
            sortable: true,
            dataIndex:'timestamp'
        },

        {
            header: "Agent",
            width: 200,
            sortable: true,
            dataIndex:'agent'
        },

        {
            header: "MessageType",
            width: 100,
            sortable: true,
            dataIndex:'msgType'
        },

        {
            header: "Sent Message",
            width: 100,
            sortable: true,
            dataIndex:'sentMsg'
        },

        {
            header: "Received Message",
            width: 100,
            sortable: true,
            dataIndex:'recMsg'
        }
        ],
        viewConfig: {
            forceFit: true
        },
        stripeRows: true,
        autoHeight:false,
        height:400,
        width:650,
        autoScroll:true,
        title:'Pushed Messages',
        buttonAlign:'left',
        buttons: [
        {
            text: 'Refresh',
            handler: function(){
                zones.refreshZonePushMessages();
            }
        }
        ]
    });

    return grid;
};

grids.createZoneReceivedMessageGrid = function(data){
    var store = new Ext.data.SimpleStore({
		url: './zone/getzonemessages',
        fields: [
        { name: 'timestamp'},
        { name: 'agent'},
        { name: 'msgType'},
        { name: 'recMsg' },
        { name: 'resMsg'}
		]
    });
    store.loadData(data);
	
    var grid = new Ext.grid.GridPanel({
        store: store,
        columns: [
        {
            header: "Log Timestamp",
            width: 125,
            sortable: true,
            dataIndex:'timestamp'
        },

        {
            header: "Agent",
            width: 125,
            sortable: true,
            dataIndex:'agent'
        },

        {
            header: "MessageType",
            width: 100,
            sortable: true,
            dataIndex:'msgType'
        },

        {
            header: "Recieved Message",
            width: 100,
            sortable: true,
            dataIndex:'recMsg'
        },

        {
            header: "Response Message",
            width: 100,
            sortable: true,
            dataIndex:'resMsg'
        }
        ],
        viewConfig: {
            forceFit: true,
			enableRowBody:true,
			            showPreview:true,
        },
        stripeRows: true,
        autoHeight:false,
        height:400,
        width:650,
        autoScroll:true,
        title:'Received Messages',
        buttonAlign:'left',
        buttons: [
        {
            text: 'Refresh',
            handler: function(){
               zones.refreshZoneReceivedMessages();
            }
        }
        ]
    });

    return grid;
};


