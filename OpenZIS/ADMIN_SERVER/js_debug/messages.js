

var msg = {};

msg.xmlMessages = function(zoneId){
    var store = new Ext.data.SimpleStore({
		url: './zone/getzonemessages',
		reader: new Ext.data.JsonReader({
			root:'rows',
			totalProperty: 'results',
			id:'logId'
		}),
        fields: [
        { name: 'logId'},
		{ name: 'timestamp'},
        { name: 'agentName'},
        { name: 'messageType'}
		]
    });

	var pagesize = 25;
	var paging_toolbar =  new Ext.PagingToolbar({
	          pageSize: pagesize,
	          store: store,
	          displayInfo: true,
	          displayMsg: 'Displaying topics {0} - {1} of {2}',
	          emptyMsg: "No topics to display"
	});

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
            dataIndex:'messageType'
        },

        {
            header: "Recieved Message",
            width: 100,
            sortable: true,
            dataIndex:'LogId'
        },

        {
            header: "Response Message",
            width: 100,
            sortable: true,
            dataIndex:'logId'
        }
        ],
        viewConfig: {
            forceFit: true,
			enableRowBody:true,
			showPreview:true,
        },
        stripeRows: true,
        autoHeight:false,
        autoScroll:true,
		height: '100%',
		width: '100%',
        title:'Received Messages',
		bbar: new Ext.PagingToolbar({
		            pageSize: 25,
		            store: store,
		            displayInfo: true,
		            displayMsg: 'Displaying topics {0} - {1} of {2}',
		            emptyMsg: "No topics to display"
		        })
    });

     store.load({params:{start:0, limit:25, lic:main.LIC, ZONE_ID:zoneId, ZIT_LOG_MESSAGE_TYPE:2}});

    return grid;
};