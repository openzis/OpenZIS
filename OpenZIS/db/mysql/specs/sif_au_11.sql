
INSERT INTO `versions` (`VERSION_ID`,`VERSION_DESC`,`VERSION_DIRECTORY`,`SCHEMA_DIRECTORY`,`VERSION_NUM`,`VERSION_NAMESPACE`,`ACTIVE`) VALUES
 (311,'AU 1.1','au_1_1_lib/','../ZIT_APPLICATION/sif_schema/au_1_0/SIF_Message.xsd.xml','1.1','http://www.sifinfo.org/au/infrastructure/2.x',1);

INSERT INTO `data_object_group` (`GROUP_ID`,`GROUP_DESC`,`VERSION_ID`) VALUES 
 (3110,'SIF AU',311);


INSERT INTO `data_object` (`OBJECT_NAME`,`GROUP_ID`,`VERSION_ID`) VALUES 
 ('CalendarDate',3110,311),
 ('CalendarSummary',3110,311),
 ('Identity',3110,311),
 ('LEAInfo',3110,311),
 ('PersonPicture',3110,311),
 ('ReportAuthorityInfo',3110,311),
 ('ReportManifest',3110,311),
 ('RoomInfo',3110,311),
 ('SchoolCourseInfo',3110,311),
 ('SchoolInfo',3110,311),
 ('SchoolPrograms',3110,311),
 ('SessionInfo',3110,311),
 ('SIF_ReportObject',3110,311),
 ('StaffAssignment',3110,311),
 ('StaffPersonal',3110,311),
 ('StudentActivityInfo',3110,311),
 ('StudentActivityParticipation',3110,311),
 ('StudentAttendanceSummary',3110,311),
 ('StudentContactPersonal',3110,311),
 ('StudentContactRelationship',3110,311),
 ('StudentDailyAttendance',3110,311),
 ('StudentParticipation',3110,311),
 ('StudentPeriodAttendance',3110,311),
 ('StudentPersonal',3110,311),
 ('StudentSchoolEnrollment',3110,311),
 ('StudentSDTN',3110,311),
 ('StudentSnapshot',3110,311),
 ('SummaryEnrollmentInfo',3110,311),
 ('TeachingGroup',3110,311),
 ('TermInfo',3110,311),
 ('TimeTable',3110,311),
 ('TimeTableCell',3110,311),
 ('TimeTableSubject',3110,311);