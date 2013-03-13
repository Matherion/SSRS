<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<!--************************************************

 Smooth Student Request System (SSRS)

 This file is part of SSRS, which is licensed
 under the Creative Commons: Attribution,
 Non-Commercial, Share Alike license (see
 http://creativecommons.org/licenses/by-nc-sa/3.0/)
 
 The first version was developed by
 Gjalt-Jorn Peters for the Dutch Open University
 in March 2013.
 
*************************************************-->

<html>
  <head>
    <meta name="robots" content="noindex, nofollow" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
    <title>
      OU Psy: aanvragen opdracht
    </title>
    <link rel="stylesheet" type="text/css" href="/request/ssrs.css" />
    <script type="text/javascript" src="/js/jquery/jquery-1.7.min.js"></script>
    <script type="text/javascript" src="/submit/jquery.validate.min.js"></script>
    <script type="text/javascript">
      $().ready(function(){
        // Javascript message
        $('#javascriptMessage').css("display","none");
        
        // JQuery validation plugin (http://bassistance.de/jquery-plugins/jquery-plugin-validation/)
        $("#form").validate({
          rules: {
            name: {
              required: true,
              minlength: 5
            },
            idnr: {
              required: true,
              number: true
            },
            email: {
              required: true,
              email: true
            }
          },
          messages: {
            name: {
              required: "Type hier je naam in.",
              minlength: "Type zowel je voor- als je achternaam."
            },
            idnr: {
              required: "Type hier je id-nummer.",
              number: "Je id-nummer mag alleen cijfers bevatten!"
            },
            email: {
              required: "Type hier je e-mail adres.",
              email: "Dit moet een valide e-mail adres zijn!"
            }
          }
        });
      });
    </script>

  </head>
  <body>
    <div class="wrapper">
      <div class="header">
        <div class="ou_logo"></div>
        Onderzoekspractica
      </div>
      <div class="gjyp_errorbox" id="javascriptMessage">
        You have Javascript disabled for this website (or in general). Please activate javascript. You can find instructions on how to activate javascript for your browser at <a href="http://www.activatejavascript.org/">http://www.activatejavascript.org/</a>.
      </div>
