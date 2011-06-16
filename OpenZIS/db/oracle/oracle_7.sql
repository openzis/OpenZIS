create or replace view authenticate as
select * from zit_admin 
where active = 1;

create or replace view data_object_group_count as
 select version_id, count(*) counter from data_object_group
  group by version_id;


create or replace view provision_dataobject_agent_vw as
select  ap.provision_id, ap.zone_id, ap.agent_id, a.source_id, ap.context_id, do.version_id, do.group_id, do.object_id, ap.publish_add, ap.publish_delete, ap.publish_change
  from  agent_provisions  ap,
        agent             a,
        data_object       do
 where ap.agent_id = a.agent_id
   and ap.object_type_id = do.object_id;

create or replace view provision_dataobject_vw as
select ap.provision_id, ap.zone_id, ap.agent_id, ap.context_id, do.version_id, do.group_id, do.object_id, do.object_name, ap.publish_add, ap.publish_delete, ap.publish_change
  from  agent_provisions  ap,
        data_object       do
 where ap.object_type_id = do.object_id;


create or replace view agentpermisiondataobject_vw as
select ap.*, do.object_name, do.group_id, do.version_id
  from agent_permissions ap,
       data_object do
 where ap.object_id = do.object_id;


create or replace view agtresponderdataobjagt_vw as
select ar.*, a.source_id, do.version_id, do.object_name
  from agent_responder ar,
       agent a,
       data_object do
 where ar.agent_id = a.agent_id
    and ar.object_type_id = do.object_id;

create or replace view requestagent_vw as
select request_id, version, max_buffer_size, source_id, request_msg_id, zone_id, context_id
  from  request r,
        agent a
  where a.agent_id = r.agent_id_requester;

create or replace view NumMessage_VW as
select zone_id, context_id, agent_id_rec agent_id from event where (status_id = 1 or status_id = 2)
union all
select zone_id, context_id, agent_id_responder agent_id from request where (status_id = 1 or status_id = 2)
union all
select zone_id, context_id, agent_id_requester agent_id from response where (status_id = 1 or status_id = 2);

create or replace view getfirstmessage_vw as
Select distinct zone_id, context_id, agent_id_responder agent_id, min(response_id) id, 1 type
  from response
 where status_id in (1,2)
 group by zone_id, context_id, agent_id_responder
union all
Select distinct zone_id, context_id, agent_id_requester agent_id, min(request_id) id, 2 type
  from request
 where status_id in (1,2)
 GROUP BY zone_id, context_id, agent_id_requester
union all
Select distinct zone_id, context_id, agent_id_rec agent_id, min(event_id) id, 3 type
  from event
 where status_id in (1,2)
 GROUP BY zone_id, context_id, agent_id_rec;
