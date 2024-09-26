<?php

namespace App\Services;

use App\Command\BudgetCommand;
use App\Enums\CMD_options;

class ExpensesStorage
{
    private $data = [];
    private $id = 1;
    private $fileName = 'data.json';

    public function __construct()
    {

        if (!file_exists($this->fileName)) {
            return file_put_contents($this->fileName, json_encode([]));
        }
        $curr_expenses = json_decode(file_get_contents($this->fileName), true);

        $this->data = $curr_expenses;
        $this->id = max(array_map(function ($v) {
            return $v['id'];
        }, $curr_expenses)) + 1;
    }

    public function addNewExpense(array $data)
    {
        $date= date("Y-m-d, H:i:s");
        $budget = new BudgetCommand();
        $budget->getBudget($date, $data[CMD_options::Amount->value]);

        $data = (object)[
            CMD_options::ID->value => $this->id,
            "date" => $date,
            CMD_options::Description->value => $data[CMD_options::Description->value],
            CMD_options::Amount->value => "$" . $data[CMD_options::Amount->value],
            CMD_options::Category->value => $data[CMD_options::Category->value]
        ];
        array_push($this->data, (object)$data);

        file_put_contents($this->fileName, json_encode($this->data, JSON_PRETTY_PRINT));
    }

    public function listExpenses(string | null $category): array
    {
        if (!$category) {
            return $this->data;
        }
        $filtered_expenses = array_filter($this->data, function ($el) use ($category) {
            return $el[CMD_options::Category->value] === $category;
        }, ARRAY_FILTER_USE_BOTH);

        return $filtered_expenses;
    }

    public function summaryExpenses(string | null $month_number): string
    {
        $total = 0;
        $month_name = null;
        foreach ($this->data as $v) {
            $value = explode("$", $v[CMD_options::Amount->value])[1];
            $expense_month = date('n', strtotime($v['date']));
            if (!$month_number) {
                $total += (int)$value;
            }
            if ($expense_month === $month_number) {
                $total += (int)$value;
                $month_name = date("F", strtotime($v['date']));
            } else {
                $month_name = date("F", mktime(0, 0, 0, $month_number));
            };
        };
        return $month_number ? "Total expenses for $month_name: $$total" : "Total expenses: $$total";
    }
    public function deleteExpense(int $id): bool
    {
        $id_exists = false;

        foreach ($this->data as $v) {
            if ($v['id'] === $id) {
                $id_exists = true;
                break;
            }
        }
        if (!$id_exists) return false;

        $this->data = array_filter($this->data, function ($v) use ($id) {
            return $id !== $v['id'];
        }, ARRAY_FILTER_USE_BOTH);
        file_put_contents($this->fileName, json_encode(array_values($this->data), JSON_PRETTY_PRINT));
        return true;
    }

    public function updateExpenses(int $id, array $data): bool
    {

        $item_to_update = null;

        $shifted_arr = array_filter($this->data, function ($v) use ($id, &$item_to_update) {
            if ((int)$v['id'] === (int)$id) {
                $item_to_update = $v;
            }
            return (int)$v['id'] !== (int)$id;
        });

        if (!$item_to_update) return false;

        foreach ($item_to_update as $k => $v) {
            if ($k !== 'date') {
                $item_to_update[$k] = (bool) $data[$k] ? $data[$k] : $v;
            };
        }
        $curr_date = date("Y-m-d, H:i:s ");
        $item_to_update['date'] = $curr_date;
        $budget = new BudgetCommand();
        $budget->getBudget($curr_date, (int)explode('$',$data[CMD_options::Amount->value])[1]);

        array_push($shifted_arr, $item_to_update);

        file_put_contents($this->fileName, json_encode(array_values($shifted_arr), JSON_PRETTY_PRINT));

        return true;
    }
}
