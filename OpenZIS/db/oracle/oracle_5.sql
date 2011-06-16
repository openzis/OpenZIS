

create or replace trigger admin_level_fctg_bi before insert on admin_level
for each row
when (
new.level_id is null
      )
begin
  select seq_admin_level.nextval into :new.level_id from dual;
end;
/


create or replace trigger agent_fctg_bi before insert on agent
for each row
when (
new.agent_id is null
      )
begin
  select seq_agent.nextval into :new.agent_id from dual;
end;
/


create or replace trigger agent_modes_fctg_bi before insert on agent_modes
for each row
when (
new.agent_mode_id is null
      )
begin
  select seq_agent_modes.nextval into :new.agent_mode_id from dual;
end;
/


create or replace trigger agent_permissions_fctg_bi before insert on agent_permissions
for each row
when (
new.permission_id is null
      )
begin
  select seq_agent_permissions.nextval into :new.permission_id from dual;
end;
/


create or replace trigger agent_provisions_fctg_bi before insert on agent_provisions
for each row
when (
new.provision_id is null
      )
begin
  select seq_agent_provisions.nextval into :new.provision_id from dual;
end;
/


create or replace trigger agent_registered_fctg_bi before insert on agent_registered
for each row
when (
new.registration_id is null
      )
begin
  select seq_agent_registered.nextval into :new.registration_id from dual;
end;
/


create or replace trigger agent_requester_fctg_bi before insert on agent_requester
for each row
when (
new.requester_id is null
      )
begin
  select seq_agent_requester.nextval into :new.requester_id from dual;
end;
/


create or replace trigger agent_responder_fctg_bi before insert on agent_responder
for each row
when (
new.responder_id is null
      )
begin
  select seq_agent_responder.nextval into :new.responder_id from dual;
end;
/


create or replace trigger agent_subscriptions_fctg_bi before insert on agent_subscriptions
for each row
when (
new.subscription_id is null
      )
begin
  select seq_agent_subscriptions.nextval into :new.subscription_id from dual;
end;
/


create or replace trigger context_fctg_bi before insert on context
for each row
when (
new.context_id is null
      )
begin
  select seq_context.nextval into :new.context_id from dual;
end;
/


create or replace trigger data_element_fctg_bi before insert on data_element
for each row
when (
new.element_id is null
      )
begin
  select seq_data_element.nextval into :new.element_id from dual;
end;
/


create or replace trigger data_object_fctg_bi before insert on data_object
for each row
when (
new.object_id is null
      )
begin
  select seq_data_object.nextval into :new.object_id from dual;
end;
/


create or replace trigger data_object_element_fctg_bi before insert on data_object_element
for each row
when (
new.data_object_element_id is null
      )
begin
  select seq_data_object_element.nextval into :new.data_object_element_id from dual;
end;
/


create or replace trigger data_object_group_fctg_bi before insert on data_object_group
for each row
when (
new.group_id is null
      )
begin
  select seq_data_object_group.nextval into :new.group_id from dual;
end;
/


create or replace trigger error_log_fctg_bi before insert on error_log
for each row
when (
new.error_id is null
      )
begin
  select seq_error_log.nextval into :new.error_id from dual;
end;
/


create or replace trigger event_fctg_bi before insert on event
for each row
when (
new.event_id is null
      )
begin
  select seq_event.nextval into :new.event_id from dual;
end;
/


create or replace trigger event_actions_fctg_bi before insert on event_actions
for each row
when (
new.action_id is null
      )
begin
  select seq_event_actions.nextval into :new.action_id from dual;
end;
/


create or replace trigger event_status_fctg_bi before insert on event_status
for each row
when (
new.status_id is null
      )
begin
  select seq_event_status.nextval into :new.status_id from dual;
end;
/


create or replace trigger group_permission_fctg_bi before insert on group_permission
for each row
when (
new.group_permission_id is null
      )
begin
  select seq_group_permission.nextval into :new.group_permission_id from dual;
end;
/


create or replace trigger group_permission_item_fctg_bi before insert on group_permission_item
for each row
when (
new.group_permission_item_id is null
      )
begin
  select seq_group_permission_item.nextval into :new.group_permission_item_id from dual;
end;
/


create or replace trigger request_fctg_bi before insert on request
for each row
when (
new.request_id is null
      )
begin
  select seq_request.nextval into :new.request_id from dual;
end;
/


create or replace trigger response_fctg_bi before insert on response
for each row
when (
new.response_id is null
      )
begin
  select seq_response.nextval into :new.response_id from dual;
end;
/


create or replace trigger versions_fctg_bi before insert on versions
for each row
when (
new.version_id is null
      )
begin
  select seq_versions.nextval into :new.version_id from dual;
end;
/


create or replace trigger zit_admin_fctg_bi before insert on zit_admin
for each row
when (
new.admin_id is null
      )
begin
  select seq_zit_admin.nextval into :new.admin_id from dual;
end;
/


create or replace trigger zit_log_fctg_bi before insert on zit_log
for each row
when (
new.log_id is null
      )
begin
  select seq_zit_log.nextval into :new.log_id from dual;
end;
/


create or replace trigger zit_server_fctg_bi before insert on zit_server
for each row
when (
new.zit_id is null
      )
begin
  select seq_zit_server.nextval into :new.zit_id from dual;
end;
/


create or replace trigger zones_fctg_bi before insert on zones
for each row
when (
new.zone_id is null
      )
begin
  select seq_zones.nextval into :new.zone_id from dual;
end;
/

