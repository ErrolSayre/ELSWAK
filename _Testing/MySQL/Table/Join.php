<?php
require_once('ELSWebAppKit/MySQL/Conditional.php');
require_once('ELSWebAppKit/MySQL/Table/Join.php');
$tableJoin = new ELSWebAppKit_MySQL_Table_Join('INNER', 'natural left outer', new ELSWebAppKit_MySQL_Table('proposal_investigators', new ELSWebAppKit_MySQL_Database('GrahamCracker')));
print_r_html($tableJoin);
$tableJoin->conditions()->addCondition(new ELSWebAppKit_MySQL_Conditional(new ELSWebAppKit_MySQL_Literal('proposals.PROPOSAL_ID'), new ELSWebAppKit_MySQL_Operator('='), new ELSWebAppKit_MySQL_Literal('proposal_investigators.PROPOSAL_ID')));
$tableJoin->conditions()->addCondition(new ELSWebAppKit_MySQL_Conditional(new ELSWebAppKit_MySQL_Literal('proposals.date_submitted'), new ELSWebAppKit_MySQL_Operator('>='), new ELSWebAppKit_MySQL_Literal('2000-01-01')));
print_r_html($tableJoin->sql());
?>