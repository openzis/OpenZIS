
INSERT INTO `versions` (`VERSION_ID`,`VERSION_DESC`,`VERSION_DIRECTORY`,`SCHEMA_DIRECTORY`,`VERSION_NUM`,`VERSION_NAMESPACE`,`ACTIVE`) VALUES
 (213,'UK 1.3','uk_1_3_lib/','../ZIT_APPLICATION/sif_schema/uk_1_3/SIF_Message.xsd.xml','2.4','http://www.sifinfo.org/uk/infrastructure/2.x',1);

INSERT INTO `data_object_group` (`GROUP_ID`,`GROUP_DESC`,`VERSION_ID`) VALUES 
 (2130,'SIF UK',213);


INSERT INTO `data_object` (`OBJECT_NAME`,`GROUP_ID`,`VERSION_ID`) VALUES 
('AssessmentLearnerSet',2130,213),
('AssessmentResponseComponent',2130,213),
('AssessmentResponseComponentGroup',2130,213),
('AssessmentResultComponent',2130,213),
('AssessmentResultComponentGroup',2130,213),
('AssessmentResultGradeSet',2130,213),
('AssessmentSession',2130,213),
('ContactPersonal',2130,213),
('Cycle',2130,213),
('Identity',2130,213),
('Junction',2130,213),
('LAInfo',2130,213),
('LearnerAssessmentResponseSet',2130,213),
('LearnerAssessmentResult',2130,213),
('LearnerAttendance',2130,213),
('LearnerAttendanceSummary',2130,213),
('LearnerBehaviourIncident',2130,213),
('LearnerContact',2130,213),
('LearnerEntitlement',2130,213),
('LearnerExclusion',2130,213),
('LearnerGroupEnrolment',2130,213),
('LearnerPersonal',2130,213),
('LearnerSchoolEnrolment',2130,213),
('LearnerSpecialNeeds',2130,213),
('Lesson',2130,213),
('NonTeachingActivity',2130,213),
('PersonDietaryPreference',2130,213),
('PersonPicture',2130,213),
('SchoolGroup',2130,213),
('SchoolGroupType',2130,213),
('SchoolInfo',2130,213),
('SchoolMealStatus',2130,213),
('Scope',2130,213),
('TeachingGroup',2130,213),
('TermInfo',2130,213),
('TTRoom',2130,213),
('TTSite',2130,213),
('TTSubject',2130,213),
('TTTeacher',2130,213),
('WorkforcePersonal',2130,213);