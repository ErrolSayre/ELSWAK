<?php
// include some files
require_once('ELSWebAppKit/MySQL/From/Clause.php');

// set up some data
$grahamCracker = new ELSWebAppKit_MySQL_Database('GrahamCracker');
$proposals = new ELSWebAppKit_MySQL_Table('proposals', $grahamCracker);
$proposalInvestigators = new ELSWebAppKit_MySQL_Table('proposal_investigators', $grahamCracker);
$proposalAwards = new ELSWebAppKit_MySQL_Table('proposal_awards', $grahamCracker);

echo 'Using Where Conditions';

$from = new ELSWebAppKit_MySQL_From_Clause();
$from->addTable($proposals);
print_r_html($from->sql());
$from->addTable($proposalInvestigators);
print_r_html($from->sql());
$from->addTable($proposalAwards);
print_r_html($from->sql());

echo 'Using Joins';

// set up some more data
$proposalsProposalId = new ELSWebAppKit_MySQL_Field('PROPOSAL_ID', $proposals, 'int');
$proposalInvestigatorsProposalId = new ELSWebAppKit_MySQL_Field('PROPOSAL_ID', $proposalInvestigators, 'int');
$proposalAwardsProposalId = new ELSWebAppKit_MySQL_Field('PROPOSAL_ID', $proposalAwards, 'int');

$from = new ELSWebAppKit_MySQL_From_Clause();
$from->addTable(new ELSWebAppKit_MySQL_Table('proposals', new ELSWebAppKit_MySQL_Database('GrahamCracker')));
print_r_html($from->sql());
$from->addTableJoin
(
	new ELSWebAppKit_MySQL_Table_Join
	(
		null,
		'LEFT',
		$proposalInvestigators,
		new ELSWebAppKit_MySQL_Conditional_Group
		(
			array
			(
				new ELSWebAppKit_MySQL_Conditional
				(
					$proposalsProposalId,
					new ELSWebAppKit_MySQL_Operator('='),
					$proposalInvestigatorsProposalId
				)
			),
			new ELSWebAppKit_MySQL_Conjunction('AND')
		)
	)
);
print_r_html($from->sql());
$from->addTableJoin
(
	new ELSWebAppKit_MySQL_Table_Join
	(
		null,
		'LEFT',
		$proposalAwards,
		new ELSWebAppKit_MySQL_Conditional_Group
		(
			array
			(
				new ELSWebAppKit_MySQL_Conditional
				(
					$proposalsProposalId,
					new ELSWebAppKit_MySQL_Operator('='),
					$proposalAwardsProposalId
				)
			),
			new ELSWebAppKit_MySQL_Conjunction('AND')
		)
	)
);
print_r_html($from->sql());
?>