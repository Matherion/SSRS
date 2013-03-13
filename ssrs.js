/***************************************************

 Smooth Student Request System (SSRS)

 This file is part of SSRS, which is licensed
 under the Creative Commons: Attribution,
 Non-Commercial, Share Alike license (see
 http://creativecommons.org/licenses/by-nc-sa/3.0/)
 
 The first version was developed by
 Gjalt-Jorn Peters for the Dutch Open University
 in March 2013.
 
***************************************************/

showBlock = function (id) {
  if (id=="pe") {
    $("#pe").css("display","block");
  } else {
    $("#pe").css("display","none");
  }
  if (id=="ls") {
    $("#ls").css("display","block");
  } else {
    $("#ls").css("display","none");
  }
  if (id=="ps_abc") {
    $("#ps_abc").css("display","block");
  } else {
    $("#ps_abc").css("display","none");
  }
  if (id=="ps_d") {
    $("#ps_d").css("display","block");
  } else {
    $("#ps_d").css("display","none");
  }
  $("#begeleider").css("display","block");
  $('input[name=course]').val(id);
}