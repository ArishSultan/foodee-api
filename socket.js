var express = require('express')
    , http = require('http');
var app = express();
var server = http.createServer(app);
var io = require('socket.io').listen(server);

var Redis = require('ioredis');

var redis = new Redis();

io.on('connection', function(socket) {
});

redis.psubscribe('*', function(err, count) {
});

redis.on('pmessage', function(subscribed, channel, message) {
    message = JSON.parse(message);
    var myDate = new Date();
    var datestring =  myDate.getUTCFullYear()+"-"+myDate.getUTCMonth()+"-"+myDate.getUTCDay()+" "+ myDate.getUTCHours() + ":" + myDate.getUTCMinutes() + ":" + myDate.getUTCSeconds();
    var log = "["+datestring+"] local.INFO: Channel: "+channel+"  Event: "+message.event+" Message: "+JSON.stringify(message.data);
    console.log(log);
    io.emit(channel + ':' + message.event, message.data);
});

server.listen(3000);