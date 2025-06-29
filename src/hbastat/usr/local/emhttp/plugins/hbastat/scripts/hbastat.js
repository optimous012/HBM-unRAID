/*
  MIT License

  Copyright (c) 2025 optimous012

  Permission is hereby granted, free of charge, to any person obtaining a copy
  of this software and associated documentation files (the "Software"), to deal
  in the Software without restriction, including without limitation the rights
  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
  copies of the Software, and to permit persons to whom the Software is
  furnished to do so, subject to the following conditions:

  The above copyright notice and this permission notice shall be included in all
  copies or substantial portions of the Software.

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
  SOFTWARE.
*/

const hbastat_status = () => {
  $.getJSON("/plugins/hbastat/hbastatus.php", (data) => {
    if (data) {
      $(".hba-temp").removeAttr("style").css("width", data["temp"]);

      $.each(data, function (key, data) {
        $(".hba-" + key).html(data);
      });
    }
  });
};

const hbastat_dash = () => {
  // append data from the table into the correct one
  $("#db-box1").append($(".dash_hbastat").html());

  // reload toggle to get the correct state
  toggleView("dash_hbastat_toggle", true);

  // reload sorting to get the stored data (cookie)
  sortTable($("#db-box1"), $.cookie("db-box1"));
};

/*
TODO: Not currently used due to issue with default reset actually working
function resetDATA(form) {
    form.TEMPFORMAT.value = "C";
    form.DISPTEMP.value = "1";
    form.DISPSESSIONS.value = "1";
    form.UIREFRESH.value = "1";
    form.UIREFRESHINT.value = "1000";
}
*/
