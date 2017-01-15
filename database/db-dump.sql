-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Erstellungszeit: 15. Jan 2017 um 10:29
-- Server-Version: 10.1.20-MariaDB
-- PHP-Version: 7.0.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Datenbank: `musical-pancake`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `installed_packages`
--

CREATE TABLE `installed_packages` (
  `id` bigint(20) NOT NULL,
  `systems_id` bigint(20) NOT NULL,
  `datetime` datetime NOT NULL,
  `package_name` varchar(100) COLLATE utf8_bin NOT NULL,
  `package_version` varchar(20) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

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
-- Indizes für die Tabelle `installed_packages`
--
ALTER TABLE `installed_packages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `systems_id` (`systems_id`);

--
-- Indizes für die Tabelle `systems`
--
ALTER TABLE `systems`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `installed_packages`
--
ALTER TABLE `installed_packages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=553;
--
-- AUTO_INCREMENT für Tabelle `systems`
--
ALTER TABLE `systems`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
