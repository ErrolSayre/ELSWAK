<?php
// include some files
require_once('DummyConnection.php');
require_once('ELSWebAppKit/MySQL/Select.php');

// set up some data
$grahamCracker = new ELSWebAppKit_MySQL_Database('GrahamCracker');
$proposals = new ELSWebAppKit_MySQL_Table('proposals', $grahamCracker);
$proposalInvestigators = new ELSWebAppKit_MySQL_Table('proposal_investigators', $grahamCracker);
$proposalAwards = new ELSWebAppKit_MySQL_Table('proposal_awards', $grahamCracker);
$proposalsProposalId = new ELSWebAppKit_MySQL_Field('PROPOSAL_ID', $proposals, 'int');
$proposalInvestigatorsProposalId = new ELSWebAppKit_MySQL_Field('PROPOSAL_ID', $proposalInvestigators, 'int');
$proposalInvestigatorsPrimaryInvestigator = new ELSWebAppKit_MySQL_Field('primary_investigator', $proposalInvestigators, 'enum');
$proposalAwardsProposalId = new ELSWebAppKit_MySQL_Field('PROPOSAL_ID', $proposalAwards, 'int');

// build the select clause
$select = new ELSWebAppKit_MySQL_Select_Clause();
$select->addField($proposalsProposalId);
$select->addField(new ELSWebAppKit_MySQL_Field('proposal_title', $proposals, 'varchar(100)'));
$select->addField(new ELSWebAppKit_MySQL_Field('date_start', $proposals, 'datetime'));
$select->addField(new ELSWebAppKit_MySQL_Field('date_end', $proposals, 'datetime'));
$select->addField(new ELSWebAppKit_MySQL_Field('award_amount', $proposalAwards, 'double(10,2)'));
$select->addField($proposalInvestigatorsPrimaryInvestigator);

// build the from clause
$from = new ELSWebAppKit_MySQL_From_Clause();
$from->addTable(new ELSWebAppKit_MySQL_Table('proposals', new ELSWebAppKit_MySQL_Database('GrahamCracker')));
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
				),
				new ELSWebAppKit_MySQL_Conditional
				(
					$proposalInvestigatorsPrimaryInvestigator,
					new ELSWebAppKit_MySQL_Operator('='),
					new ELSWebAppKit_MySQL_String('YES', $db)
				)
			),
			new ELSWebAppKit_MySQL_Conjunction('AND')
		)
	)
);
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

// build the where clause
$where = new ELSWebAppKit_MySQL_Where_Clause
(
	array
	(
		new ELSWebAppKit_MySQL_Conditional
		(
			$proposalsProposalId,
			new ELSWebAppKit_MySQL_Operator('='),
			$proposalInvestigatorsProposalId
		),
		new ELSWebAppKit_MySQL_Conditional
		(
			$proposalInvestigatorsPrimaryInvestigator,
			new ELSWebAppKit_MySQL_Operator('='),
			new ELSWebAppKit_MySQL_String('YES', $db)
		),
		new ELSWebAppKit_MySQL_Conditional
		(
			$proposalsProposalId,
			new ELSWebAppKit_MySQL_Operator('='),
			$proposalAwardsProposalId
		),
		new ELSWebAppKit_MySQL_Conditional_Group
		(
			array
			(
				new ELSWebAppKit_MySQL_Conditional
				(
					new ELSWebAppKit_MySQL_Field('date_start', $proposals, 'datetime'),
					new ELSWebAppKit_MySQL_Operator('<'),
					new ELSWebAppKit_MySQL_String('2008-07-01', $db)
				),
				new ELSWebAppKit_MySQL_Conditional
				(
					new ELSWebAppKit_MySQL_Field('date_end', $proposals, 'datetime'),
					new ELSWebAppKit_MySQL_Operator('>'),
					new ELSWebAppKit_MySQL_String('2008-07-01', $db)
				)
			),
			new ELSWebAppKit_MySQL_Conjunction('OR')
		)
	),
	new ELSWebAppKit_MySQL_Conjunction('AND')
);

// build the order clause
$order = new ELSWebAppKit_MySQL_Order_Clause();
$order->addOrdinal
(
	new ELSWebAppKit_MySQL_Ordinal
	(
		new ELSWebAppKit_MySQL_Field('proposal_title', $proposals, 'varchar(100)')
	)
);
$order->addOrdinal
(
	new ELSWebAppKit_MySQL_Ordinal
	(
		new ELSWebAppKit_MySQL_Field('date_submitted', $proposals, 'datetime')
	)
);

// create a new select query
$select = new ELSWebAppKit_MySQL_Select($select, $from, $where, $order);
echo '<h1>Default</h1>'.LF;
print_r_html($select->sql(''));
echo '<h1>field</h1>'.LF;
print_r_html($select->sql('field'));
echo '<h1>table.field</h1>'.LF;
print_r_html($select->sql('table.field'));
echo '<h1>database.table.field</h1>'.LF;
print_r_html($select->sql('database.table.field'));
?>