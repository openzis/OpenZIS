/*
Only run this if you are creating two Oracle Accounts and would like a readonly account.  You may want to use another account name other than read only.
*/

Grant Select on AGENT to READONLY;

Grant Select on ADMIN_LEVEL to READONLY;

Grant Select on AGENT_FILTERS to READONLY;

Grant Select on AGENT_MODES to READONLY;

Grant Select on AGENT_PERMISSIONS to READONLY;

Grant Select on AGENT_PROVISIONS to READONLY;

Grant Select on AGENT_REGISTERED to READONLY;

Grant Select on AGENT_REQUESTER to READONLY;

Grant Select on AGENT_RESPONDER to READONLY;

Grant Select on AGENT_SUBSCRIPTIONS to READONLY;

Grant Select on AGENT_ZONE_CONTEXT to READONLY;

Grant Select on CONTEXT to READONLY;

Grant Select on DATA_ELEMENT to READONLY;

Grant Select on DATA_ELEMENT_CHILD to READONLY;

Grant Select on DATA_OBJECT to READONLY;

Grant Select on DATA_OBJECT_ELEMENT to READONLY;

Grant Select on DATA_OBJECT_GROUP to READONLY;

Grant Select on ERROR_LOG to READONLY;

Grant Select on EVENT to READONLY;

Grant Select on EVENT_ARCHIEVE to READONLY;

Grant Select on EVENT_ACTIONS to READONLY;

Grant Select on EVENT_STATUS to READONLY;

Grant Select on GROUP_PERMISSION to READONLY;

Grant Select on GROUP_PERMISSION_ITEM to READONLY;

Grant Select on LOG_MESSAGE_TYPE to READONLY;

Grant Select on PUSH_HANDLER to READONLY;

Grant Select on REQUEST to READONLY;

Grant Select on REQUEST_ARCHIEVE to READONLY;

Grant Select on RESPONSE_ARCHIEVE to READONLY;

Grant Select on RESPONSE to READONLY;

Grant Select on SIF_AUTHENTICATION_LEVEL to READONLY;

Grant Select on SIF_MESSAGE_TYPE to READONLY;

Grant Select on VERSIONS to READONLY;

Grant Select on ZIT_ADMIN to READONLY;

Grant Select on ZIT_LOG to READONLY;

Grant Select on ZIT_LOG_ARCHIEVE to READONLY;

Grant Select on ZIT_SERVER to READONLY;

Grant Select on ZONES to READONLY;

Grant Select on ZONE_AUTHENTICATION_TYPE to READONLY;

Grant Select on MESSAGE to READONLY;

Grant Select on AUTHENTICATE to READONLY;

Grant Select on DATA_OBJECT_GROUP_COUNT to READONLY;

Grant Select on PROVISION_DATAOBJECT_AGENT_VW to READONLY;

Grant Select on PROVISION_DATAOBJECT_VW to READONLY;

Grant Select on AGENTPERMISIONDATAOBJECT_VW to READONLY;

Grant Select on AGTRESPONDERDATAOBJAGT_VW to READONLY;

Grant Select on REQUESTAGENT_VW to READONLY;

Grant Select on NUMMESSAGE_VW to READONLY;

Grant Select on GETFIRSTMESSAGE_VW to READONLY;
