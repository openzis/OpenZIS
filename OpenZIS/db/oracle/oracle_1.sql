
drop table admin_level;

create table admin_level
(
	level_id 	number(2)		not null,
	level_desc 	varchar2(100) 	not null
);




drop table admin_level cascade constraints;

create table admin_level
(
  level_id    number(10)                        not null,
  level_desc  varchar2(255 byte)                default ''                    not null
)
tablespace users
pctused    0
pctfree    10
initrans   1
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
logging 
nocompress 
nocache
noparallel
monitoring;



drop table agent cascade constraints;

create table agent
(
  agent_id          number(10)                  not null,
  agent_name        varchar2(255 byte)          default ''                    not null,
  source_id         varchar2(255 byte)          default ''                    not null,
  password          varchar2(45 byte),
  username          varchar2(45 byte),
  ipaddress         varchar2(20 byte),
  maxbuffersize     varchar2(20 byte),
  cert_common_name  varchar2(4000 byte),
  cert_common_dn    varchar2(4000 byte),
  admin_id          number(10)                  default 0                     not null,
  active            number(10)                  default 1                     not null
)
tablespace users
pctused    0
pctfree    10
initrans   1
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
logging 
nocompress 
nocache
noparallel
monitoring;


drop table agent_filters cascade constraints;

create table agent_filters
(
  zone_id                 number(10)            not null,
  agent_id                number(10)            not null,
  context_id              number(10)            not null,
  data_object_element_id  number(10)            not null,
  data_element_child_id   number(10)            not null
)
tablespace users
pctused    0
pctfree    10
initrans   1
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
logging 
nocompress 
nocache
noparallel
monitoring;



drop table agent_modes cascade constraints;

create table agent_modes
(
  agent_mode_id  number(10)                     not null,
  mode_desc      varchar2(45 byte)              default ''                    not null
)
tablespace users
pctused    0
pctfree    10
initrans   1
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
logging 
nocompress 
nocache
noparallel
monitoring;



drop table agent_permissions cascade constraints;

create table agent_permissions
(
  permission_id  number(10)                     not null,
  agent_id       number(10)                     default 0                     not null,
  context_id     number(10)                     default 0                     not null,
  object_id      number(10)                     default 0                     not null,
  can_provide    number(10)                     default 0                     not null,
  can_subscribe  number(10)                     default 0                     not null,
  can_add        number(10)                     default 0                     not null,
  can_update     number(10)                     default 0                     not null,
  can_delete     number(10)                     default 0                     not null,
  can_request    number(10)                     default 0                     not null,
  can_respond    number(10)                     default 0                     not null,
  zone_id        number(10)                     default 0                     not null
)
tablespace users
pctused    0
pctfree    10
initrans   1
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
logging 
nocompress 
nocache
noparallel
monitoring;


drop table agent_provisions cascade constraints;

create table agent_provisions
(
  provision_id         number(10)               not null,
  agent_id             number(10)               default 0                     not null,
  object_type_id       number(10)               default 0                     not null,
  provision_timestamp  date                     not null,
  context_id           number(10)               default 0                     not null,
  publish_add          number(10)               default 0,
  publish_delete       number(10)               default 0,
  publish_change       number(10)               default 0,
  zone_id              number(10)               default 0                     not null
)
tablespace users
pctused    0
pctfree    10
initrans   1
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
logging 
nocompress 
nocache
noparallel
monitoring;



drop table agent_registered cascade constraints;

create table agent_registered
(
  registration_id          number(10)           not null,
  agent_id                 number(10)           default 0                     not null,
  callback_url             varchar2(255 byte),
  agent_mode_id            number(10)           default 0                     not null,
  register_timestamp       date                 not null,
  unregister_timestamp     date,
  asleep                   number(10)           default 0                     not null,
  protocol_type            varchar2(255 byte),
  sif_version              varchar2(45 byte)    default ''                    not null,
  secure                   number(10)           default 0                     not null,
  maxbuffersize            number(20)           default 0                     not null,
  zone_id                  number(10)           default 0                     not null,
  context_id               number(10)           default 0                     not null,
  frozen                   number(10)           default 0                     not null,
  frozen_msg_id            varchar2(255 byte),
  authentication_level_id  number(3)            default 0                     not null
)
tablespace users
pctused    0
pctfree    10
initrans   1
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
logging 
nocompress 
nocache
noparallel
monitoring;



drop table agent_requester cascade constraints;

create table agent_requester
(
  requester_id         number(10)               not null,
  agent_id             number(10)               default 0                     not null,
  object_type_id       number(10)               default 0                     not null,
  requester_timestamp  date                     not null,
  context_id           number(10)               default 0                     not null,
  zone_id              number(10)               default 0                     not null
)
tablespace users
pctused    0
pctfree    10
initrans   1
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
logging 
nocompress 
nocache
noparallel
monitoring;



drop table agent_responder cascade constraints;

create table agent_responder
(
  responder_id         number(10)               not null,
  agent_id             number(10)               default 0                     not null,
  object_type_id       number(10)               default 0                     not null,
  responder_timestamp  date                     not null,
  context_id           number(10)               default 0                     not null,
  zone_id              number(10)               default 0                     not null
)
tablespace users
pctused    0
pctfree    10
initrans   1
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
logging 
nocompress 
nocache
noparallel
monitoring;



drop table agent_subscriptions cascade constraints;

create table agent_subscriptions
(
  subscription_id      number(10)               not null,
  agent_id             number(10)               default 0                     not null,
  object_type_id       number(10)               default 0                     not null,
  subscribe_timestamp  date                     not null,
  context_id           number(10)               default 0                     not null,
  zone_id              number(10)               default 0                     not null
)
tablespace users
pctused    0
pctfree    10
initrans   1
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
logging 
nocompress 
nocache
noparallel
monitoring;



drop table agent_zone_context cascade constraints;

create table agent_zone_context
(
  zone_id     number(10)                        default 0                     not null,
  agent_id    number(10)                        default 0                     not null,
  context_id  number(10)                        default 0                     not null
)
tablespace users
pctused    0
pctfree    10
initrans   1
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
logging 
nocompress 
nocache
noparallel
monitoring;



drop table context cascade constraints;

create table context
(
  context_id    number(10)                      not null,
  context_desc  varchar2(255 byte)              default ''                    not null
)
tablespace users
pctused    0
pctfree    10
initrans   1
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
logging 
nocompress 
nocache
noparallel
monitoring;


drop table data_element cascade constraints;

create table data_element
(
  element_id    number(10)                      not null,
  element_name  varchar2(255 byte)              default ''                    not null,
  xml_tag_name  varchar2(255 byte)              default ''                    not null
)
tablespace users
pctused    0
pctfree    10
initrans   1
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
logging 
nocompress 
nocache
noparallel
monitoring;


drop table data_element_child cascade constraints;

create table data_element_child
(
  data_element_child_id  number(10)             not null,
  parent_element_id      number(10)             not null,
  child_element_id       number(10)             not null
)
tablespace users
pctused    0
pctfree    10
initrans   1
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
logging 
nocompress 
nocache
noparallel
monitoring;



drop table data_object cascade constraints;

create table data_object
(
  object_name  varchar2(255 byte)               default ''                    not null,
  object_id    number(10)                       not null,
  group_id     number(10)                       default 0                     not null,
  version_id   number(10)                       default 0                     not null
)
tablespace users
pctused    0
pctfree    10
initrans   1
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
logging 
nocompress 
nocache
noparallel
monitoring;



drop table data_object_element cascade constraints;

create table data_object_element
(
  data_object_element_id  number(10)            not null,
  object_id               number(10)            not null,
  element_id              number(10)            not null,
  can_filter              number(3)             not null
)
tablespace users
pctused    0
pctfree    10
initrans   1
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
logging 
nocompress 
nocache
noparallel
monitoring;



drop table data_object_group cascade constraints;

create table data_object_group
(
  group_id    number(10)                        not null,
  group_desc  varchar2(255 byte)                default ''                    not null,
  version_id  number(10)                        default 0                     not null
)
tablespace users
pctused    0
pctfree    10
initrans   1
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
logging 
nocompress 
nocache
noparallel
monitoring;


drop table error_log cascade constraints;

create table error_log
(
  error_id          number(10)                  not null,
  short_error_desc  varchar2(255 byte)          default ''                    not null,
  long_error_desc   clob                        not null,
  error_location    varchar2(255 byte)          default ''                    not null,
  error_timestamp   date                        not null,
  zone_id           number(10),
  agent_id          number(10),
  context_id        number(10),
  archived          number(3)                   default 0                     not null
)
lob (long_error_desc) store as (
  tablespace users
  enable       storage in row
  chunk       8192
  retention
  nocache
  logging
  index       (
        tablespace users
        storage    (
                    initial          64k
                    minextents       1
                    maxextents       unlimited
                    pctincrease      0
                    buffer_pool      default
                   ))
      storage    (
                  initial          64k
                  minextents       1
                  maxextents       unlimited
                  pctincrease      0
                  buffer_pool      default
                 ))
tablespace users
pctused    0
pctfree    10
initrans   1
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
logging 
nocompress 
nocache
noparallel
monitoring;


drop table event cascade constraints;

create table event
(
  event_id         number(10)                   not null,
  event_timestamp  date                         not null,
  agent_id_sender  number(10)                   default 0                     not null,
  agent_id_rec     number(10)                   default 0                     not null,
  event_data       clob                         not null,
  object_id        number(10)                   default 0                     not null,
  status_id        number(10)                   default 1                     not null,
  action_id        number(10)                   default 0                     not null,
  msg_id           varchar2(60 byte),
  agent_mode_id    number(10)                   not null,
  context_id       number(10)                   not null,
  zone_id          number(10)                   not null
)
lob (event_data) store as (
  tablespace users
  enable       storage in row
  chunk       8192
  retention
  nocache
  logging
  index       (
        tablespace users
        storage    (
                    initial          64k
                    minextents       1
                    maxextents       unlimited
                    pctincrease      0
                    buffer_pool      default
                   ))
      storage    (
                  initial          64k
                  minextents       1
                  maxextents       unlimited
                  pctincrease      0
                  buffer_pool      default
                 ))
tablespace users
pctused    0
pctfree    10
initrans   1
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
logging 
nocompress 
nocache
noparallel
monitoring;

drop table event_archieve cascade constraints;

create table event_archieve
(
  event_id         number(10)                   not null,
  event_timestamp  date                         not null,
  agent_id_sender  number(10)                   default 0                     not null,
  agent_id_rec     number(10)                   default 0                     not null,
  event_data       clob                         not null,
  object_id        number(10)                   default 0                     not null,
  status_id        number(10)                   default 1                     not null,
  action_id        number(10)                   default 0                     not null,
  msg_id           varchar2(60 byte),
  agent_mode_id    number(10)                   not null,
  context_id       number(10)                   not null,
  zone_id          number(10)                   not null
)
lob (event_data) store as (
  tablespace users
  enable       storage in row
  chunk       8192
  retention
  nocache
  logging
  index       (
        tablespace users
        storage    (
                    initial          64k
                    minextents       1
                    maxextents       unlimited
                    pctincrease      0
                    buffer_pool      default
                   ))
      storage    (
                  initial          64k
                  minextents       1
                  maxextents       unlimited
                  pctincrease      0
                  buffer_pool      default
                 ))
tablespace users
pctused    0
pctfree    10
initrans   1
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
logging 
nocompress 
nocache
noparallel
monitoring;


drop table event_actions cascade constraints;

create table event_actions
(
  action_id    number(10)                       not null,
  action_desc  varchar2(45 byte)                default ''                    not null
)
tablespace users
pctused    0
pctfree    10
initrans   1
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
logging 
nocompress 
nocache
noparallel
monitoring;



drop table event_status cascade constraints;

create table event_status
(
  status_id    number(10)                       not null,
  status_desc  varchar2(45 byte)                default ''                    not null
)
tablespace users
pctused    0
pctfree    10
initrans   1
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
logging 
nocompress 
nocache
noparallel
monitoring;


drop table group_permission cascade constraints;

create table group_permission
(
  group_permission_id  number(10)               not null,
  group_name           varchar2(255 byte)       default ''                    not null,
  created_timestamp    date                     not null,
  updated_timestamp    date                     not null,
  admin_id             number(10)               default 0                     not null,
  version_id           number(10)               default 0                     not null
)
tablespace users
pctused    0
pctfree    10
initrans   1
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
logging 
nocompress 
nocache
noparallel
monitoring;



drop table group_permission_item cascade constraints;

create table group_permission_item
(
  group_permission_item_id  number(10)          not null,
  object_id                 number(10)          default 0                     not null,
  can_provide               number(10)          default 0                     not null,
  can_subscribe             number(10)          default 0                     not null,
  can_add                   number(10)          default 0                     not null,
  can_update                number(10)          default 0                     not null,
  can_delete                number(10)          default 0                     not null,
  can_request               number(10)          default 0                     not null,
  can_respond               number(10)          default 0                     not null,
  group_permission_id       number(10)          default 0                     not null
)
tablespace users
pctused    0
pctfree    10
initrans   1
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
logging 
nocompress 
nocache
noparallel
monitoring;



drop table log_message_type cascade constraints;

create table log_message_type
(
  log_message_type_id    number(10)             not null,
  log_message_type_desc  varchar2(255 byte)     default ''                    not null
)
tablespace users
pctused    0
pctfree    10
initrans   1
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
logging 
nocompress 
nocache
noparallel
monitoring;



drop table push_handler cascade constraints;

create table push_handler
(
  zone_id             number(10)                not null,
  context_id          number(10)                not null,
  push_running        number(3)                 default 0                     not null,
  last_start          date                      not null,
  last_stop           date,
  php_pid             number(10),
  sleep_time_seconds  number(10)
)
tablespace users
pctused    0
pctfree    10
initrans   1
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
logging 
nocompress 
nocache
noparallel
monitoring;



drop table request;

create table request
(
  request_id          number(10)                not null,
  request_msg_id      varchar2(255 byte)        default ''                    not null,
  request_data        clob                      not null,
  request_timestamp   date                      not null,
  status_id           number(10)                default 1                     not null,
  agent_id_requester  number(10)                default 0                     not null,
  agent_id_responder  number(10)                default 0                     not null,
  max_buffer_size     varchar2(255 byte)        default ''                    not null,
  version             varchar2(255 byte)        default ''                    not null,
  msg_id              varchar2(60 byte),
  agent_mode_id       number(10)                not null,
  context_id          number(10)                not null,
  zone_id             number(10)                not null
)
lob (request_data) store as (
  tablespace users
  enable       storage in row
  chunk       8192
  retention
  nocache
  logging
  index       (
        tablespace users
        storage    (
                    initial          64k
                    minextents       1
                    maxextents       unlimited
                    pctincrease      0
                    buffer_pool      default
                   ))
      storage    (
                  initial          64k
                  minextents       1
                  maxextents       unlimited
                  pctincrease      0
                  buffer_pool      default
                 ))
tablespace users
pctused    0
pctfree    10
initrans   1
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
logging 
nocompress 
nocache
noparallel
monitoring;


drop table request_archieve;

create table request_archieve
(
  request_id          number(10)                not null,
  request_msg_id      varchar2(255 byte)        default ''                    not null,
  request_data        clob                      not null,
  request_timestamp   date                      not null,
  status_id           number(10)                default 1                     not null,
  agent_id_requester  number(10)                default 0                     not null,
  agent_id_responder  number(10)                default 0                     not null,
  max_buffer_size     varchar2(255 byte)        default ''                    not null,
  version             varchar2(255 byte)        default ''                    not null,
  msg_id              varchar2(60 byte),
  agent_mode_id       number(10)                not null,
  context_id          number(10)                not null,
  zone_id             number(10)                not null
)
lob (request_data) store as (
  tablespace users
  enable       storage in row
  chunk       8192
  retention
  nocache
  logging
  index       (
        tablespace users
        storage    (
                    initial          64k
                    minextents       1
                    maxextents       unlimited
                    pctincrease      0
                    buffer_pool      default
                   ))
      storage    (
                  initial          64k
                  minextents       1
                  maxextents       unlimited
                  pctincrease      0
                  buffer_pool      default
                 ))
tablespace users
pctused    0
pctfree    10
initrans   1
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
logging 
nocompress 
nocache
noparallel
monitoring;


drop table response_archieve;

create table response_archieve
(
  response_id         number(10)                not null,
  request_msg_id      varchar2(255 byte)        default ''                    not null,
  response_data       clob                      not null,
  next_packet_num     number(10)                default 0                     not null,
  status_id           number(10)                default 1                     not null,
  agent_id_requester  number(10)                default 0                     not null,
  agent_id_responder  number(10)                default 0                     not null,
  msg_id              varchar2(60 byte),
  agent_mode_id       number(10)                not null,
  context_id          number(10)                not null,
  zone_id             number(10)                not null
)
lob (response_data) store as (
  tablespace users
  enable       storage in row
  chunk       8192
  retention
  nocache
  logging
  index       (
        tablespace users
        storage    (
                    initial          64k
                    minextents       1
                    maxextents       unlimited
                    pctincrease      0
                    buffer_pool      default
                   ))
      storage    (
                  initial          64k
                  minextents       1
                  maxextents       unlimited
                  pctincrease      0
                  buffer_pool      default
                 ))
tablespace users
pctused    0
pctfree    10
initrans   1
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
logging 
nocompress 
nocache
noparallel
monitoring;


drop table response;

create table response
(
  response_id         number(10)                not null,
  request_msg_id      varchar2(255 byte)        default ''                    not null,
  response_data       clob                      not null,
  next_packet_num     number(10)                default 0                     not null,
  status_id           number(10)                default 1                     not null,
  agent_id_requester  number(10)                default 0                     not null,
  agent_id_responder  number(10)                default 0                     not null,
  msg_id              varchar2(60 byte),
  agent_mode_id       number(10)                not null,
  context_id          number(10)                not null,
  zone_id             number(10)                not null
)
lob (response_data) store as (
  tablespace users
  enable       storage in row
  chunk       8192
  retention
  nocache
  logging
  index       (
        tablespace users
        storage    (
                    initial          64k
                    minextents       1
                    maxextents       unlimited
                    pctincrease      0
                    buffer_pool      default
                   ))
      storage    (
                  initial          64k
                  minextents       1
                  maxextents       unlimited
                  pctincrease      0
                  buffer_pool      default
                 ))
tablespace users
pctused    0
pctfree    10
initrans   1
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
logging 
nocompress 
nocache
noparallel
monitoring;


drop table sif_authentication_level;

create table sif_authentication_level
(
  sif_authentication_level_id    number(10)     not null,
  sif_authentication_level_desc  varchar2(255 byte) default '' not null
);
drop table sif_message_type;

create table sif_message_type
(
  sif_message_type_id    number(10)             not null,
  sif_message_type_desc  varchar2(255 byte)     default ''                    not null
);

drop table versions;

create table versions
(
  version_id         number(10)                 not null,
  version_desc       varchar2(255 byte)         default ''                    not null,
  version_directory  varchar2(255 byte)         default ''                    not null,
  schema_directory   varchar2(255 byte)         default ''                    not null,
  version_num        varchar2(10 byte)          default ''                    not null,
  version_namespace  varchar2(255 byte)         default ''                    not null,
  active             number(10)                 default 0                     not null
);

drop table zit_admin;

create table zit_admin
(
  admin_id        number(10)                    not null,
  admin_username  varchar2(45 byte)             default ''                    not null,
  admin_password  varchar2(45 byte)             default ''                    not null,
  zit_id          number(10)                    default 0                     not null,
  admin_level_id  number(10)                    default 2                     not null,
  first_name      varchar2(255 byte),
  last_name       varchar2(255 byte),
  email           varchar2(255 byte),
  active          number(10)                    default 0                     not null,
  last_login      date,
  attempts	  number(10) 			default 0,
  lockout	  date
);


drop table zit_log;

create table zit_log
(
  log_id               number(10)               not null,
  create_timestamp     date                     not null,
  rec_xml              clob                     not null,
  sent_xml             clob                     not null,
  zone_id              number(10)               default 0                     not null,
  agent_id             number(10),
  sif_message_type_id  number(10)               not null,
  log_message_type_id  number(10)               not null,
  archived             number(3)                default 0                     not null
)
lob (rec_xml) store as (
  tablespace users
  enable       storage in row
  chunk       8192
  retention
  nocache
  logging
  index       (
        tablespace users
        storage    (
                    initial          64k
                    minextents       1
                    maxextents       unlimited
                    pctincrease      0
                    buffer_pool      default
                   ))
      storage    (
                  initial          64k
                  minextents       1
                  maxextents       unlimited
                  pctincrease      0
                  buffer_pool      default
                 ))
lob (sent_xml) store as (
  tablespace users
  enable       storage in row
  chunk       8192
  retention
  nocache
  logging
  index       (
        tablespace users
        storage    (
                    initial          64k
                    minextents       1
                    maxextents       unlimited
                    pctincrease      0
                    buffer_pool      default
                   ))
      storage    (
                  initial          64k
                  minextents       1
                  maxextents       unlimited
                  pctincrease      0
                  buffer_pool      default
                 ))
tablespace users
pctused    0
pctfree    10
initrans   1
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
nologging 
nocompress 
nocache
noparallel
monitoring;

drop table zit_log_archieve;

create table zit_log_archieve
(
  log_id               number(10)               not null,
  create_timestamp     date                     not null,
  rec_xml              clob                     not null,
  sent_xml             clob                     not null,
  zone_id              number(10)               default 0                     not null,
  agent_id             number(10),
  sif_message_type_id  number(10)               not null,
  log_message_type_id  number(10)               not null,
  archived             number(3)                default 0                     not null
)
lob (rec_xml) store as (
  tablespace users
  enable       storage in row
  chunk       8192
  retention
  nocache
  logging
  index       (
        tablespace users
        storage    (
                    initial          64k
                    minextents       1
                    maxextents       unlimited
                    pctincrease      0
                    buffer_pool      default
                   ))
      storage    (
                  initial          64k
                  minextents       1
                  maxextents       unlimited
                  pctincrease      0
                  buffer_pool      default
                 ))
lob (sent_xml) store as (
  tablespace users
  enable       storage in row
  chunk       8192
  retention
  nocache
  logging
  index       (
        tablespace users
        storage    (
                    initial          64k
                    minextents       1
                    maxextents       unlimited
                    pctincrease      0
                    buffer_pool      default
                   ))
      storage    (
                  initial          64k
                  minextents       1
                  maxextents       unlimited
                  pctincrease      0
                  buffer_pool      default
                 ))
tablespace users
pctused    0
pctfree    10
initrans   1
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
nologging 
nocompress 
nocache
noparallel
monitoring;

drop table zit_server;

create table zit_server
(
  zit_id      number(10)                        not null,
  source_id   varchar2(255 byte)                default ''                    not null,
  asleep      number(10)                        default 0                     not null,
  admin_url   varchar2(255 byte)                default ''                    not null,
  zit_name    varchar2(255 byte)                default ''                    not null,
  min_buffer  varchar2(255 byte)                default ''                    not null,
  max_buffer  varchar2(255 byte)                default ''                    not null,
  zit_url     varchar2(255 byte)                default ''                    not null
);


drop table zones;

create table zones
(
  zone_id                      number(10)       not null,
  zone_desc                    varchar2(255 byte) default '' not null,
  source_id                    varchar2(255 byte) default '' not null,
  create_timestamp             date             not null,
  update_timestamp             date,
  admin_id                     number(10)       default 0                     not null,
  version_id                   number(10)       default 0                     not null,
  zone_authentication_type_id  number(10)       default 1                     not null,
  sleeping                     number(10)       default 0                     not null
);


drop table zone_authentication_type;

create table zone_authentication_type
(
  zone_authentication_type_id    number(10)     not null,
  zone_authentication_type_desc  varchar2(255 byte) default '' not null
);


create table message
(
id						number,
zone_id					number(5)			default 0,
context_id				number(2)			default 0,
message					clob,
message_type_id			number(2)			default 0,
message_next_packet_num	number(6)			default 0,
message_object_id		number(6)			default 0,
message_timestamp		date				default sysdate,
agent_id_from			number(5)			default 0,
agent_from_msgid		varchar2(60)			default '',
agent_from_version		varchar2(10)			default '',
agent_from_max_buffer_size	number				default 0,
agent_id_to				number(5)			default 0,
agent_to_msgid			varchar2(60)			default '',
agent_to_mode_id		number(4)			default 0,
agent_to_status_id		number(2)			default 1
)
lob (message) store as (
  tablespace users
  enable       storage in row
  chunk       8192
  retention
  nocache
  logging
  index       (
        tablespace users
        storage    (
                    initial          64k
                    minextents       1
                    maxextents       unlimited
                    pctincrease      0
                    buffer_pool      default
                   ))
      storage    (
                  initial          64k
                  minextents       1
                  maxextents       unlimited
                  pctincrease      0
                  buffer_pool      default
                 ))
tablespace users
pctused    0
pctfree    10
initrans   1
maxtrans   255
storage    (
            initial          64k
            minextents       1
            maxextents       unlimited
            pctincrease      0
            buffer_pool      default
           )
logging 
nocompress 
nocache
noparallel
monitoring;