alter table admin_level add (
  constraint primary_key31
  primary key
  (level_id)
  using index primary_key31);

alter table agent add (
  constraint primary_key23
  primary key
  (agent_id)
  using index primary_key23);

alter table agent_filters add (
  constraint primary_key27
  primary key
  (zone_id, agent_id, context_id, data_object_element_id, data_element_child_id)
  using index primary_key27);

alter table agent_modes add (
  constraint primary_key15
  primary key
  (agent_mode_id)
  using index primary_key15);

alter table agent_permissions add (
  constraint primary_key17
  primary key
  (permission_id)
  using index primary_key17);

alter table agent_provisions add (
  constraint primary_key1
  primary key
  (provision_id)
  using index primary_key1);

alter table agent_registered add (
  constraint primary_key4
  primary key
  (registration_id)
  using index primary_key4);

alter table agent_requester add (
  constraint primary_key25
  primary key
  (requester_id)
  using index primary_key25);

alter table agent_responder add (
  constraint primary_key16
  primary key
  (responder_id)
  using index primary_key16);

alter table agent_subscriptions add (
  constraint primary_key12
  primary key
  (subscription_id)
  using index primary_key12);

alter table agent_zone_context add (
  constraint primary_key29
  primary key
  (zone_id, agent_id, context_id)
  using index primary_key29);

alter table context add (
  constraint primary_key32
  primary key
  (context_id)
  using index primary_key32);

alter table data_element add (
  constraint primary_key30
  primary key
  (element_id)
  using index primary_key30);

alter table data_element_child add (
  constraint primary_key7
  primary key
  (parent_element_id, child_element_id, data_element_child_id)
  using index primary_key7);

alter table data_object add (
  constraint primary_key8
  primary key
  (object_id)
  using index primary_key8);

alter table data_object_element add (
  constraint primary_key18
  primary key
  (data_object_element_id)
  using index primary_key18);

alter table data_object_group add (
  constraint primary_key2
  primary key
  (group_id)
  using index primary_key2);

alter table error_log add (
  constraint primary_key33
  primary key
  (error_id)
  using index primary_key33);

alter table event add (
  constraint primary_key21
  primary key
  (event_id)
  using index primary_key21);

alter table event_actions add (
  constraint primary_key19
  primary key
  (action_id)
  using index primary_key19);

alter table event_status add (
  constraint primary_key13
  primary key
  (status_id)
  using index primary_key13);

alter table group_permission add (
  constraint primary_key34
  primary key
  (group_permission_id)
  using index primary_key34);

alter table group_permission_item add (
  constraint primary_key26
  primary key
  (group_permission_item_id)
  using index primary_key26);

alter table log_message_type add (
  constraint primary_key28
  primary key
  (log_message_type_id)
  using index primary_key28);

alter table push_handler add (
  constraint primary_key9
  primary key
  (zone_id, context_id)
  using index primary_key9);

alter table request add (
  constraint primary_key
  primary key
  (request_id)
  using index primary_key);

alter table response add (
  constraint primary_key20
  primary key
  (response_id)
  using index primary_key20);

alter table sif_authentication_level add (
  constraint primary_key10
  primary key
  (sif_authentication_level_id)
  using index primary_key10);

alter table sif_message_type add (
  constraint primary_key22
  primary key
  (sif_message_type_id)
  using index primary_key22);

alter table versions add (
  constraint primary_key24
  primary key
  (version_id)
  using index primary_key24);

alter table zit_admin add (
  constraint primary_key3
  primary key
  (admin_id)
  using index primary_key3);

alter table zit_log add (
  constraint primary_key6
  primary key
  (log_id)
  using index primary_key6);

alter table zit_server add (
  constraint primary_key5
  primary key
  (zit_id)
  using index primary_key5);

alter table zones add (
  constraint primary_key14
  primary key
  (zone_id)
  using index primary_key14);

alter table zone_authentication_type add (
  constraint primary_key11
  primary key
  (zone_authentication_type_id)
  using index primary_key11);
