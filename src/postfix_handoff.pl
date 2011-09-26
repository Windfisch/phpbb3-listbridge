#!/usr/bin/perl -w

#
# forum-list bridge 
# Copyright (C) 2010 Joel Uckelman
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program. If not, see <http://www.gnu.org/licenses/>.
#

use LWP::UserAgent;
use HTTP::Request::Common qw(POST);

local $/;
my $msg = <STDIN>;

my $url = 'http://localhost/list_post_receive.php';

my $ua = LWP::UserAgent->new;
my $req = POST $url, [ message => $msg ];

my $res = $ua->request($req);

unless ($res->is_success()) {
  die 'POST failed: ' . $res->status_line . ' ' . $res->content;
}
