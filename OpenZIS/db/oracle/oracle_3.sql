create unique index primary_key on request
(request_id)
logging
tablespace users
pctfree    10
initrans   2
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
noparallel;


create unique index primary_key1 on agent_provisions
(provision_id)
logging
tablespace users
pctfree    10
initrans   2
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
noparallel;


create unique index primary_key10 on sif_authentication_level
(sif_authentication_level_id)
logging
tablespace users
pctfree    10
initrans   2
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
noparallel;


create unique index primary_key11 on zone_authentication_type
(zone_authentication_type_id)
logging
tablespace users
pctfree    10
initrans   2
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
noparallel;


create unique index primary_key12 on agent_subscriptions
(subscription_id)
logging
tablespace users
pctfree    10
initrans   2
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
noparallel;


create unique index primary_key13 on event_status
(status_id)
logging
tablespace users
pctfree    10
initrans   2
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
noparallel;


create unique index primary_key14 on zones
(zone_id)
logging
tablespace users
pctfree    10
initrans   2
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
noparallel;


create unique index primary_key15 on agent_modes
(agent_mode_id)
logging
tablespace users
pctfree    10
initrans   2
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
noparallel;


create unique index primary_key16 on agent_responder
(responder_id)
logging
tablespace users
pctfree    10
initrans   2
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
noparallel;


create unique index primary_key17 on agent_permissions
(permission_id)
logging
tablespace users
pctfree    10
initrans   2
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
noparallel;


create unique index primary_key18 on data_object_element
(data_object_element_id)
logging
tablespace users
pctfree    10
initrans   2
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
noparallel;


create unique index primary_key19 on event_actions
(action_id)
logging
tablespace users
pctfree    10
initrans   2
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
noparallel;


create unique index primary_key2 on data_object_group
(group_id)
logging
tablespace users
pctfree    10
initrans   2
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
noparallel;


create unique index primary_key20 on response
(response_id)
logging
tablespace users
pctfree    10
initrans   2
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
noparallel;


create unique index primary_key21 on event
(event_id)
logging
tablespace users
pctfree    10
initrans   2
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
noparallel;


create unique index primary_key22 on sif_message_type
(sif_message_type_id)
logging
tablespace users
pctfree    10
initrans   2
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
noparallel;


create unique index primary_key23 on agent
(agent_id)
logging
tablespace users
pctfree    10
initrans   2
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
noparallel;


create unique index primary_key24 on versions
(version_id)
logging
tablespace users
pctfree    10
initrans   2
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
noparallel;


create unique index primary_key25 on agent_requester
(requester_id)
logging
tablespace users
pctfree    10
initrans   2
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
noparallel;


create unique index primary_key26 on group_permission_item
(group_permission_item_id)
logging
tablespace users
pctfree    10
initrans   2
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
noparallel;


create unique index primary_key27 on agent_filters
(zone_id, agent_id, context_id, data_object_element_id, data_element_child_id)
logging
tablespace users
pctfree    10
initrans   2
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
noparallel;


create unique index primary_key28 on log_message_type
(log_message_type_id)
logging
tablespace users
pctfree    10
initrans   2
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
noparallel;


create unique index primary_key29 on agent_zone_context
(zone_id, agent_id, context_id)
logging
tablespace users
pctfree    10
initrans   2
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
noparallel;


create unique index primary_key3 on zit_admin
(admin_id)
logging
tablespace users
pctfree    10
initrans   2
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
noparallel;


create unique index primary_key30 on data_element
(element_id)
logging
tablespace users
pctfree    10
initrans   2
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
noparallel;


create unique index primary_key31 on admin_level
(level_id)
logging
tablespace users
pctfree    10
initrans   2
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
noparallel;


create unique index primary_key32 on context
(context_id)
logging
tablespace users
pctfree    10
initrans   2
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
noparallel;


create unique index primary_key33 on error_log
(error_id)
logging
tablespace users
pctfree    10
initrans   2
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
noparallel;


create unique index primary_key34 on group_permission
(group_permission_id)
logging
tablespace users
pctfree    10
initrans   2
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
noparallel;


create unique index primary_key4 on agent_registered
(registration_id)
logging
tablespace users
pctfree    10
initrans   2
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
noparallel;


create unique index primary_key5 on zit_server
(zit_id)
logging
tablespace users
pctfree    10
initrans   2
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
noparallel;


create unique index primary_key6 on zit_log
(log_id)
logging
tablespace users
pctfree    10
initrans   2
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
noparallel;


create unique index primary_key7 on data_element_child
(parent_element_id, child_element_id, data_element_child_id)
logging
tablespace users
pctfree    10
initrans   2
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
noparallel;


create unique index primary_key8 on data_object
(object_id)
logging
tablespace users
pctfree    10
initrans   2
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
noparallel;


create unique index primary_key9 on push_handler
(zone_id, context_id)
logging
tablespace users
pctfree    10
initrans   2
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
noparallel;
