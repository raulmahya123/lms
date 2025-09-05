<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Glitch Effect 403</title>
  <style>
    svg {
      position: absolute;
      left: 10vw;
      top: 10vh;
      width: 80vw;
      height: 80vh;
    }

    body {
      font-family: 'Notable', sans-serif;
      word-break: break-all;
      font-size: 5rem;
      line-height: 0.75;
      overflow: hidden;
      text-align: center;
      color: red;
      background: #111;
    }

    .content {
      position: relative;
      z-index: 2;
    }
  </style>
</head>
<body>
  <!-- teks yang di-loop -->
  <div class="content">
    <span id="text-loop">THE CONTENT YOU DESIRE</span>
  </div>

  <!-- efek glitch SVG -->
  <svg viewBox="0 0 24.5 24">
    <defs>
      <filter id="filter" x="-20%" y="-10%" width="140%" height="140%" filterUnits="objectBoundingBox" primitiveUnits="userSpaceOnUse" color-interpolation-filters="linearRGB">
        <feComposite in="colormatrix" in2="SourceAlpha" operator="in" result="composite"/>
        <feTurbulence type="turbulence" baseFrequency="0 3" numOctaves="5" seed="0" stitchTiles="stitch" result="turbulence1" id="seed-changer"/>
        <feDisplacementMap in="composite" in2="turbulence1" scale="1" xChannelSelector="R" yChannelSelector="B" result="displacementMap"/>
      </filter>
    </defs>
    <path filter="url(#filter)" d="M12,0A12,12 0 0,1 24,12A12,12 0 0,1 12,24A12,12 0 0,1 0,12A12,12 0 0,1 12,0M12,2A10,10 0 0,0 2,12C2,14.4 2.85,16.6 4.26,18.33L18.33,4.26C16.6,2.85 14.4,2 12,2M12,22A10,10 0 0,0 22,12C22,9.6 21.15,7.4 19.74,5.67L5.67,19.74C7.4,21.15 9.6,22 12,22Z"></path>
  </svg>

  <script>
    // efek noise glitch
    const seedEl = document.querySelector("#seed-changer");

    const jigger = () => {
      setTimeout(() => {
        seedEl.setAttribute("seed", Math.random() * 1000);
        jigger();
      }, Math.floor(Math.random() * 550));
    };
    jigger();

    // animasi teks berjalan
    const sent = "THE CONTENT YOU DESIRE";
    const len = sent.length;
    let i = len - 1;

    setInterval(() => {
      document.body.insertAdjacentHTML('afterbegin', sent[i]);
      i--;
      if (i < 0) i = len - 1;
    }, 1000);
  </script>
</body>
</html>
