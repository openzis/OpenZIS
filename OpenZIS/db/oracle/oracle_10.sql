alter table 
   agent 
add
    (
	CERT_COMMON_NAME VARCHAR2(30000) default null,
	CERT_COMMON_DA   VARCHAR2(10000) DEFAULT NULL
);