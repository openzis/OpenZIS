create index zit_log_archieved On zit_log(archived);
create index zit_log_idx On zit_log(archived, zone_id, agent_id, log_message_type_id);

create index response_idx On response(ZONE_ID, agent_id_requester, status_id, context_id);

create index request_idx On request(zone_id, agent_id_responder, status_id, context_id);

create index event_idx on event(zone_id, agent_id_rec, status_id, context_id);

create index event_msgid_idx On event(zone_id, msg_id);