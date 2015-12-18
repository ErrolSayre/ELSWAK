<?php
class ELSWAK_URI_FactoryTest
	extends PHPUnit_Framework_TestCase {

	public function testURIComponentParsing() {
		$componentizer = new ELSWAK_URI_Componentizer;

		$tests = array(
			                    'Apple Computer Inc.' => 'apple-computer-inc',
			                        'GRADUATE SCHOOL' => 'graduate-school',
			                  'SCHOOL OF ACCOUNTANCY' => 'school-of-accountancy',
			                   ' SCHOOL OF EDUCATION' => 'school-of-education',
			                        'george clooney ' => 'george-clooney',
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



	/**
	 * The component label parsing should result in labels with minimal reformatting, in fact, in
	 * this particular case, the outputs should exactly match the inputs. A sublass could then use
	 * this infrastructure in order to perform some common actions (e.g. removing "School of" or
	 * contracting Telecommunications to Telecom).
	 */
	public function testURILabelParsing() {
		$componentizer = new ELSWAK_URI_Componentizer;

		$tests = array(
			                    'Apple Computer Inc.' => 'Apple Computer Inc.',
			                        'GRADUATE SCHOOL' => 'GRADUATE SCHOOL',
			                  'SCHOOL OF ACCOUNTANCY' => 'SCHOOL OF ACCOUNTANCY',
			                   ' SCHOOL OF EDUCATION' => 'SCHOOL OF EDUCATION',
			                        'george clooney ' => 'george clooney',
			                          'JOHN C. RILEY' => 'JOHN C. RILEY',
			                      'DougLas MacArthur' => 'DougLas MacArthur',
			            'THE COLLEGE OF LIBERAL ARTS' => 'THE COLLEGE OF LIBERAL ARTS',
			                               'MATH 262' => 'MATH 262',
			       'SCHOOL OF JOURNALISM & NEW MEDIA' => 'SCHOOL OF JOURNALISM & NEW MEDIA',
			         'COMPUTER & INFORMATION SCIENCE' => 'COMPUTER & INFORMATION SCIENCE',
			                 'ELECTRICAL ENGINEERING' => 'ELECTRICAL ENGINEERING',
			       'GEOLOGY & GEOLOGICAL ENGINEERING' => 'GEOLOGY & GEOLOGICAL ENGINEERING',
			                 'MECHANICAL ENGINEERING' => 'MECHANICAL ENGINEERING',
			                     'TELECOMMUNICATIONS' => 'TELECOMMUNICATIONS',
			              'Fancy Pants/The Other one' => 'Fancy Pants/The Other one',
			            'Fancy Pants / The Other one' => 'Fancy Pants / The Other one',
			             'Fancy Pants/ The Other one' => 'Fancy Pants/ The Other one',
			             'Fancy Pants /The Other one' => 'Fancy Pants /The Other one',
			'SARAH ISOM CTR FOR WOMEN&GENDER STUDIES' => 'SARAH ISOM CTR FOR WOMEN&GENDER STUDIES',
			 'HEALTH, EXERCISE SCI & RECREATION MGMT' => 'HEALTH, EXERCISE SCI & RECREATION MGMT',
			 'MANAGEMENT INFO SYSTEMS/PROD OPER MGMT' => 'MANAGEMENT INFO SYSTEMS/PROD OPER MGMT',
		);
		foreach ( $tests as $test => $expected ) {
			$this->assertEquals( $expected, $componentizer->parseURILabel( $test ) );
		}
	}
}