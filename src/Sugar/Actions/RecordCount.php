<?php

// Enrico Simonetti
// enricosimonetti.com

namespace Toothpaste\Sugar\Actions;
use Toothpaste\Sugar\Instance;
use Toothpaste\Sugar;

class RecordCount extends Sugar\BaseAction
{
    public function count()
    {
        $total = 0;
        $results = [];
        $db = \DBManagerFactory::getInstance();
        $tables = $db->getTablesArray();
        asort($tables);
        foreach ($tables as $table) {
            $columns = $db->get_columns($table);
            if ($key = array_search('id', array_column($columns, 'name'))) {
                $count_field = 'id';
            } else if ($key = array_search('name', array_column($columns, 'name'))) {
                $count_field = 'name';
            } else {
                $count_field = '*';
            }
            if (!empty($count_field)) {
                $qb = $db->getConnection()->createQueryBuilder();
                $qb->select('COUNT(' . $count_field . ') as count');
                $qb->from($table);
                $res = $qb->execute();
                if ($row = $res->fetch()) {
                    $results[$table] = $row['count'];
                    $total += $results[$table];
                }
                // only show records that have a count
                if (!empty($results[$table])) {
                    $this->writeln($table . ' has ' . $results[$table] . ' records');
                }
            }
        }
        $results['total_db_records'] = $total;

        $this->writeln('');
        $this->writeln('The database has in total ' . $total . ' records');
        $this->writeln('');

        //$this->writeln('JSON output:');
        //$this->writeln(json_encode($results));
        //$this->writeln(PHP_EOL);

        $this->writeln('CSV output:' . PHP_EOL);
        $this->writeln('"table","count"');
        foreach ($results as $table => $count) {
            $this->writeln('"' . $table . '","' . $count . '"');
        }
        $this->writeln('');
    }
}
