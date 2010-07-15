#!/usr/bin/perl -w

use LWP::UserAgent;
use HTTP::Request::Common qw(POST);

local $/;
my $msg = <STDIN>;

my $url = 'http://www.test2.nomic.net/list_post_receive.php';

my $ua = LWP::UserAgent->new;
my $req = POST $url, [ message => $msg ];

my $res = $ua->request($req);

unless ($res->is_success()) {
  die 'POST failed: ' . $res->status_line . ' ' . $res->content;
}
