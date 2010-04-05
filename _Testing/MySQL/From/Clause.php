<?php
// include some files
require_once('ELSWAK/MySQL/From/Clause.php');

// set up some data
$grahamCracker = new ELSWAK_MySQL_Database('GrahamCracker');
$proposals = new ELSWAK_MySQL_Table('proposals', $grahamCracker);
$proposalInvestigators = new ELSWAK_MySQL_Table('proposal_investigators', $grahamCracker);
$proposalAwards = new ELSWAK_MySQL_Table('proposal_awards', $grahamCracker);

echo 'Using Where Conditions';

$from = new ELSWAK_MySQL_From_Clause();
$from->addTable($proposals);
print_r_html($from->sql());
$from->addTable($proposalInvestigators);
print_r_html($from->sql());
$from->addTable($proposalAwards);
print_r_html($from->sql());

echo 'Using Joins';

// set up some more data
$proposalsProposalId = new ELSWAK_MySQL_Field('PROPOSAL_ID', $proposals, 'int');
$proposalInvestigatorsProposalId = new ELSWAK_MySQL_Field('PROPOSAL_ID', $proposalInvestigators, 'int');
$proposalAwardsProposalId = new ELSWAK_MySQL_Field('PROPOSAL_ID', $proposalAwards, 'int');

$from = new ELSWAK_MySQL_From_Clause();
$from->addTable(new ELSWAK_MySQL_Table('proposals', new ELSWAK_MySQL_Database('GrahamCracker')));
print_r_html($from->sql());
$from->addTableJoin
(
	new ELSWAK_MySQL_Table_Join
	(
		null,
		'LEFT',
		$proposalInvestigators,
		new ELSWAK_MySQL_Conditional_Group
		(
			array
			(
				new ELSWAK_MySQL_Conditional
				(
					$proposalsProposalId,
					new ELSWAK_MySQL_Operator('='),
					$proposalInvestigatorsProposalId
				)
			),
			new ELSWAK_MySQL_Conjunction('AND')
		)
	)
);
print_r_html($from->sql());
$from->addTableJoin
(
	new ELSWAK_MySQL_Table_Join
	(
		null,
		'LEFT',
		$proposalAwards,
		new ELSWAK_MySQL_Conditional_Group
		(
			array
			(
				new ELSWAK_MySQL_Conditional
				(
					$proposalsProposalId,
					new ELSWAK_MySQL_Operator('='),
					$proposalAwardsProposalId
				)
			),
			new ELSWAK_MySQL_Conjunction('AND')
		)
	)
);
print_r_html($from->sql());
?>