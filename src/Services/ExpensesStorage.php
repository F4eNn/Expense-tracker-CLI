<?php

namespace App\Services;

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

        $data = (object)[
            "id" => $this->id,
            "date" => date("Y-m-d, H:i:s"),
            "description" => $data['description'],
            "amount" => "$" . $data["amount"],
            "category" => $data['category']
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
            return $el['category'] === $category;
        }, ARRAY_FILTER_USE_BOTH);

        return $filtered_expenses;
    }

    public function summaryExpenses(string | null $month_number): string
    {
        $total = 0;
        $month_name = null;
        foreach ($this->data as $v) {
            $value = explode("$", $v['amount'])[1];
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
    public function deleteExpense(int $id): void
    {
        $this->data = array_filter($this->data, function ($v) use ($id) {
            return $id !== $v['id'];
        }, ARRAY_FILTER_USE_BOTH);
        file_put_contents($this->fileName, json_encode(array_values($this->data), JSON_PRETTY_PRINT));
    }
}
