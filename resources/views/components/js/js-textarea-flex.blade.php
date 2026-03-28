{{-- <x-input.textarea-flex /> を使用するための js --}}
<script type="module">
  $(function () {
      document.querySelectorAll(".FlexTextarea").forEach(flexTextarea);
  });

  function flexTextarea(el) {
      const dummy = el.querySelector(".FlexTextarea__dummy");
      el.querySelector(".FlexTextarea__textarea").addEventListener(
          "input",
          (e) => {
              dummy.textContent = e.target.value + "\u200b";
          }
      );
  }
</script>
