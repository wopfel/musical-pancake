-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Erstellungszeit: 14. Jan 2017 um 16:24
-- Server-Version: 10.1.20-MariaDB
-- PHP-Version: 7.0.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Datenbank: `musical-pancake`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `systems`
--

CREATE TABLE `systems` (
  `id` bigint(20) NOT NULL,
  `guid` char(36) COLLATE utf8_bin NOT NULL,
  `dn` varchar(150) COLLATE utf8_bin NOT NULL,
  `servername` varchar(50) COLLATE utf8_bin NOT NULL,
  `created` datetime NOT NULL,
  `last_contact` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `systems`
--
ALTER TABLE `systems`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `systems`
--
ALTER TABLE `systems`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
