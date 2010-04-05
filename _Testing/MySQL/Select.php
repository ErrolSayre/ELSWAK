<?php
// include some files
require_once('DummyConnection.php');
require_once('ELSWAK/MySQL/Select.php');

// set up some data
$grahamCracker = new ELSWAK_MySQL_Database('GrahamCracker');
$proposals = new ELSWAK_MySQL_Table('proposals', $grahamCracker);
$proposalInvestigators = new ELSWAK_MySQL_Table('proposal_investigators', $grahamCracker);
$proposalAwards = new ELSWAK_MySQL_Table('proposal_awards', $grahamCracker);
$proposalsProposalId = new ELSWAK_MySQL_Field('PROPOSAL_ID', $proposals, 'int');
$proposalInvestigatorsProposalId = new ELSWAK_MySQL_Field('PROPOSAL_ID', $proposalInvestigators, 'int');
$proposalInvestigatorsPrimaryInvestigator = new ELSWAK_MySQL_Field('primary_investigator', $proposalInvestigators, 'enum');
$proposalAwardsProposalId = new ELSWAK_MySQL_Field('PROPOSAL_ID', $proposalAwards, 'int');

// build the select clause
$select = new ELSWAK_MySQL_Select_Clause();
$select->addField($proposalsProposalId);
$select->addField(new ELSWAK_MySQL_Field('proposal_title', $proposals, 'varchar(100)'));
$select->addField(new ELSWAK_MySQL_Field('date_start', $proposals, 'datetime'));
$select->addField(new ELSWAK_MySQL_Field('date_end', $proposals, 'datetime'));
$select->addField(new ELSWAK_MySQL_Field('award_amount', $proposalAwards, 'double(10,2)'));
$select->addField($proposalInvestigatorsPrimaryInvestigator);

// build the from clause
$from = new ELSWAK_MySQL_From_Clause();
$from->addTable(new ELSWAK_MySQL_Table('proposals', new ELSWAK_MySQL_Database('GrahamCracker')));
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
				),
				new ELSWAK_MySQL_Conditional
				(
					$proposalInvestigatorsPrimaryInvestigator,
					new ELSWAK_MySQL_Operator('='),
					new ELSWAK_MySQL_String('YES', $db)
				)
			),
			new ELSWAK_MySQL_Conjunction('AND')
		)
	)
);
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

// build the where clause
$where = new ELSWAK_MySQL_Where_Clause
(
	array
	(
		new ELSWAK_MySQL_Conditional
		(
			$proposalsProposalId,
			new ELSWAK_MySQL_Operator('='),
			$proposalInvestigatorsProposalId
		),
		new ELSWAK_MySQL_Conditional
		(
			$proposalInvestigatorsPrimaryInvestigator,
			new ELSWAK_MySQL_Operator('='),
			new ELSWAK_MySQL_String('YES', $db)
		),
		new ELSWAK_MySQL_Conditional
		(
			$proposalsProposalId,
			new ELSWAK_MySQL_Operator('='),
			$proposalAwardsProposalId
		),
		new ELSWAK_MySQL_Conditional_Group
		(
			array
			(
				new ELSWAK_MySQL_Conditional
				(
					new ELSWAK_MySQL_Field('date_start', $proposals, 'datetime'),
					new ELSWAK_MySQL_Operator('<'),
					new ELSWAK_MySQL_String('2008-07-01', $db)
				),
				new ELSWAK_MySQL_Conditional
				(
					new ELSWAK_MySQL_Field('date_end', $proposals, 'datetime'),
					new ELSWAK_MySQL_Operator('>'),
					new ELSWAK_MySQL_String('2008-07-01', $db)
				)
			),
			new ELSWAK_MySQL_Conjunction('OR')
		)
	),
	new ELSWAK_MySQL_Conjunction('AND')
);

// build the order clause
$order = new ELSWAK_MySQL_Order_Clause();
$order->addOrdinal
(
	new ELSWAK_MySQL_Ordinal
	(
		new ELSWAK_MySQL_Field('proposal_title', $proposals, 'varchar(100)')
	)
);
$order->addOrdinal
(
	new ELSWAK_MySQL_Ordinal
	(
		new ELSWAK_MySQL_Field('date_submitted', $proposals, 'datetime')
	)
);

// create a new select query
$select = new ELSWAK_MySQL_Select($select, $from, $where, $order);
echo '<h1>Default</h1>'.LF;
print_r_html($select->sql(''));
echo '<h1>field</h1>'.LF;
print_r_html($select->sql('field'));
echo '<h1>table.field</h1>'.LF;
print_r_html($select->sql('table.field'));
echo '<h1>database.table.field</h1>'.LF;
print_r_html($select->sql('database.table.field'));
?>