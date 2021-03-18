<?php
namespace Chat;

use Chat\Interfaces\ConnectedClientInterface;
use Ratchet\ConnectionInterface;

class BasicMultiRoomServer extends AbstractMultiRoomServer
{

    protected function makeUserWelcomeMessage(ConnectedClientInterface $client, $timestamp)
    {
        return vsprintf('Dobro došli %s!', array($client->getName()));
    }

    protected function makeUserConnectedMessage(ConnectedClientInterface $client, $timestamp)
    {
        return vsprintf('%s se priključio chat-u', array($client->getName()));
    }

    protected function makeUserDisconnectedMessage(ConnectedClientInterface $client, $timestamp)
    {
        return vsprintf('%s je napustio chat', array($client->getName()));
    }

    protected function makeMessageReceivedMessage(ConnectedClientInterface $from, $message, $timestamp)
    {
        return $message;
    }

    protected function logMessageReceived(ConnectedClientInterface $from, $message, $timestamp)
    {
        /** save messages to a database, etc... */
    }

    protected function createClient(ConnectionInterface $conn, $name)
    {
        $client = new ConnectedClient;
        $client->setResourceId($conn->resourceId);
        $client->setConnection($conn);
        $client->setName($name);

        return $client;
    }

}