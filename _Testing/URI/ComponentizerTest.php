<?php
class ELSWAK_URI_FactoryTest
	extends PHPUnit_Framework_TestCase {

	public function testURIComponentParsing() {
		$componentizer = new ELSWAK_URI_Componentizer;
		
		$tests = array(
			                    'Apple Computer Inc.' => 'apple-computer-inc',
			                        'GRADUATE SCHOOL' => 'graduate-school',
			                  'SCHOOL OF ACCOUNTANCY' => 'school-of-accountancy',
			                    'SCHOOL OF EDUCATION' => 'school-of-education',
			                         'george clooney' => 'george-clooney',
			                          'JOHN C. RILEY' => 'john-c-riley',
			                      'DougLas MacArthur' => 'douglas-macarthur',
			            'THE COLLEGE OF LIBERAL ARTS' => 'the-college-of-liberal-arts',
			                               'MATH 262' => 'math-262',
			       'SCHOOL OF JOURNALISM & NEW MEDIA' => 'school-of-journalism-new-media',
			         'COMPUTER & INFORMATION SCIENCE' => 'computer-information-science',
			                 'ELECTRICAL ENGINEERING' => 'electrical-engineering',
			       'GEOLOGY & GEOLOGICAL ENGINEERING' => 'geology-geological-engineering',
			                 'MECHANICAL ENGINEERING' => 'mechanical-engineering',
			                     'TELECOMMUNICATIONS' => 'telecommunications',
			              'Fancy Pants/The Other one' => 'fancy-pants-the-other-one',
			            'Fancy Pants / The Other one' => 'fancy-pants-the-other-one',
			             'Fancy Pants/ The Other one' => 'fancy-pants-the-other-one',
			             'Fancy Pants /The Other one' => 'fancy-pants-the-other-one',
			'SARAH ISOM CTR FOR WOMEN&GENDER STUDIES' => 'sarah-isom-ctr-for-women-gender-studies',
			 'HEALTH, EXERCISE SCI & RECREATION MGMT' => 'health-exercise-sci-recreation-mgmt',
			 'MANAGEMENT INFO SYSTEMS/PROD OPER MGMT' => 'management-info-systems-prod-oper-mgmt',
			                                   'Fall' => 'fall',
			                          'Fall Semester' => 'fall-semester',
			                          'second SPRING' => 'second-spring',
			                           'First Summer' => 'first-summer',
			                            'full-summer' => 'full-summer',
			                          'second-Summer' => 'second-summer',
			                                 'summer' => 'summer',
		);
		foreach ( $tests as $test => $expected ) {
			$this->assertEquals( $expected, $componentizer->parseURIComponent( $test ) );
		}
	}
}