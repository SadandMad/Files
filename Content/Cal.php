<form  method="POST">
    <input type="number" name="year" min="1970" max="2030"/><br><br>
    <input type="submit" value="Показать календарь">
</form>

<?php
 // Получаем год для составления календаря
    if(isset($_POST['year'])){
        $year = $_POST['year'];
        $table = calendar($year);
        echo "$table";
    }

function calendar($year)
{
    // Объявление констант для работы скрипта
    // Пустой первый элемент для удобства обращения по индексу
    $shortDays = array('', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс');
    $months = array('', 'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь');
    // Место сохранения итоговой таблицы
    $saveFile = 'Cal.html';
    // Файл с информацией о праздниках и массив для её загрузки
    $holidaysFile = 'Holidays.inf';
    $holidays = file("Holidays.inf");
    $hNum = 0;
    $hMax = count($holidays)/3;
    // Непосредственно сам скрипт
    // Поток для сохранения HTML документа (целиком)
    ob_start();
    echo "<!DOCTYPE html>\n<html>\n<head>\n<meta charset=\"utf-8\">\n<title>Календарь</title>\n</head>\n<body>";
    // Поток для сохранения HTML таблицы
    ob_start();
    // Начинаем составление календаря - заголовок с годом
    echo "<table border=\"1\"><caption valign=\"top\">$year</caption></tbody><tr>\n";
    // Составляем отдельный календарь для каждого месяца (отдельная вложенная таблица)
    for ($month=1; $month < 13; $month++) {
        // Заголовок с месяцем
        echo "<td><table><caption valign=\"top\">$months[$month]</caption>\n<thead>\n<tr>";
        // Строка с днями недели
        for ($day=1; $day<8 ; $day++) { 
            echo "<th>$shortDays[$day]</th>";
        }
        echo "</tr>\n</thead><tbody>\n<tr>";
        // Получаем день недели первого числа месяца и количество дней в месяце
        $firstDay = date('N', mktime(0, 0, 0, $month, 1, $year));
        $amountDays = date('t', mktime(0, 0, 0, $month, 1, $year));
        // Заполняем первую строку календаря месяца
        for ($day=1; $day<$firstDay; $day++) { 
            echo "<td>  </td>";
        }
        for ($day = 1; $firstDay<8; $firstDay++, $day++) {
            if ($hNum < $hMax && $month == $holidays[$hNum*3+1] && $day == $holidays[$hNum*3]) {
                echo "<td title=\"";
                echo $holidays[$hNum*3+2];
                echo "\" style=\"color: red\"><b> $day</b></td>";
                $hNum++;
            }
            else{
                echo "<td> $day</td>";
            }
        }
        echo "</tr>\n";
        // И остальные строки, пока не проставим все дни
        while ($day <= $amountDays) {
            echo "<tr>";
            for ($weekDay=1; $weekDay<8 ; $weekDay++, $day++) {
                // Проверяем, является ли день праздником
                if ($hNum < $hMax && $month == $holidays[$hNum*3+1] && $day == $holidays[$hNum*3]) {
                    echo "<td title=\"";
                    echo $holidays[$hNum*3+2];
                    echo "\" style=\"color: red\"><b> $day</b></td>";
                    $hNum++;
                }
                else{
                    echo "<td> $day</td>";
                }
                // Дошли до конца месяца - прерываем заполнение недели
                if ($day == $amountDays) {
                    $day++;
                    break;
                }
            }
            echo "</tr>\n";
        }
        echo "</tbody></table></td>\n";
        // Перенос строки после 4 месяцев (4 в строке)
        if ($month == 4 or $month==8) {
            echo "</tr><tr>\n";
        }
    }
    // Получение строкового представления полученных HTML таблицы и страницы
    echo "\n</tr></tbody></table>";
    $table = ob_get_contents();
    echo "\n</body>\n</html>";
    $page = ob_get_contents();
    ob_end_clean();
    ob_end_clean();
    // Сохранение полученной страницы в файл, возврат таблицы в вызвавший поток
    file_put_contents($saveFile, $page);
    return $table;
}
?>