<?php

function createCompetition($comp_name, $start_date, $end_date, $payment_id, $show_forms, $show_bus, $show_room, $comp_desc, $hidden): bool
{
    require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
    $sql_conn = getDBConn();

    $create_competition_stmt = $sql_conn->prepare(
        "INSERT INTO competitions (competition_name, start_date, end_date, payment_id, show_forms, show_bus, show_room, description, hidden)
               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );

    if (empty($payment_id))
        $payment_id = null;
    $create_competition_stmt->bind_param('ssssiiisi',
        $comp_name, $start_date, $end_date, $payment_id,
        $show_forms, $show_bus, $show_room,
        $comp_desc, $hidden
    );

    return $create_competition_stmt->execute() && $sql_conn->close();
}

function updateCompetition($comp_name, $start_date, $end_date, $payment_id, $show_forms, $show_bus, $show_room, $comp_desc, $hidden): bool
{
    require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
    $sql_conn = getDBConn();

    $update_competition_stmt = $sql_conn->prepare(
        "UPDATE competitions SET start_date = ?, end_date = ?, payment_id = ?, show_forms = ?, show_bus = ?, show_room = ?, description = ?, hidden = ?
               WHERE competition_name = ?"
    );

    if (empty($payment_id))
        $payment_id = null;
    $update_competition_stmt->bind_param('sssiiissi',
        $start_date, $end_date, $payment_id,
        $show_forms, $show_bus, $show_room,
        $comp_desc, $hidden,
        $comp_name
    );

    return $update_competition_stmt->execute() && $sql_conn->close();
}

function hideCompetition($comp_name): bool
{
    require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
    $sql_conn = getDBConn();

    // Update hidden attribute
    $hide_comp_stmt = $sql_conn->prepare("UPDATE competitions SET hidden = 1 WHERE competition_name = ?");
    $hide_comp_stmt->bind_param('s', $comp_name);
    $result_comp = $hide_comp_stmt->execute();

    $sql_conn->close();
    return $result_comp;
}

function deleteCompetition($comp_name): bool
{
    require_once $_SERVER['DOCUMENT_ROOT'] . "/shared/sql.php";
    $sql_conn = getDBConn();

    // Competition
    $delete_comp_stmt = $sql_conn->prepare("DELETE FROM competitions WHERE competition_name = ?");
    $delete_comp_stmt->bind_param('s', $comp_name);
    $result_comp = $delete_comp_stmt->execute();

    // Competition Data
    $delete_comp_data_stmt = $sql_conn->prepare("DELETE FROM competition_data WHERE competition_name = ?");
    $delete_comp_data_stmt->bind_param('s', $comp_name);
    $result_comp_data = $delete_comp_data_stmt->execute();

    // Competition Selections
    $delete_comp_selections_stmt = $sql_conn->prepare("DELETE FROM competition_selections WHERE competition_name = ?");
    $delete_comp_selections_stmt->bind_param('s', $comp_name);
    $result_comp_selections = $delete_comp_selections_stmt->execute();

    $sql_conn->close();
    return ($result_comp && $result_comp_data && $result_comp_selections);
}