<?php
class AdminPhoneCustomerSearchController extends ModuleAdminController
{
    public function ajaxProcessSearch()
    {
        if (!$this->context->employee || !$this->context->employee->isLoggedBack()) {
            http_response_code(403);
            exit;
        }

        $query = (string) Tools::getValue('query', '');
        $digits = strtr($query, array(
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
            '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
            '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
        ));
        $digits = preg_replace('/\D+/', '', $digits);
        if (Tools::strlen($digits) < 7) {
            $this->jsonResponse(array('customers' => array()));
        }

        $phone = "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(a.phone_mobile, ' ', ''), '-', ''), '+', ''), '(', ''), ')', ''), '.', '')";
        $needle = pSQL($digits);
        $sql = 'SELECT c.id_customer, c.firstname, c.lastname, c.email, MAX(a.phone_mobile) AS phone_mobile '
            . 'FROM `' . _DB_PREFIX_ . 'customer` c '
            . 'INNER JOIN `' . _DB_PREFIX_ . 'address` a ON (a.id_customer = c.id_customer) '
            . 'WHERE c.deleted = 0 AND a.deleted = 0 AND a.phone_mobile <> "" '
            . 'AND ' . $phone . ' LIKE "%' . $needle . '%" '
            . 'GROUP BY c.id_customer, c.firstname, c.lastname, c.email '
            . 'ORDER BY c.id_customer DESC LIMIT 20';

        $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        $this->jsonResponse(array('customers' => $rows ?: array()));
    }

    private function jsonResponse($data)
    {
        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($data));
    }
}
