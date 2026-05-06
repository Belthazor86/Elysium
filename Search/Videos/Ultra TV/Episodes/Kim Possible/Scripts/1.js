document.addEventListener('DOMContentLoaded', () => {
  const container = document.body;

  const videoSets = [
    { video: 'x7mjeys' },  // Car Trouble
    { video: 'x7mogus' },  // Rewriting History
    { video: 'x7mjey9' },  // Two to Tutor
    { video: 'x7mqbid' },  // Emotion Sickness
    { video: 'x7m67ug' },  // Stop Team Go
    { video: 'x7m67xa' },  // Clean Slate
    { video: 'x6pb58j' },  // Fashion Victim
    { video: 'x7mmk0n' } // Blush
  ];

  let currentIndex = 0;

  function loadDMAPI(callback) {
    const script = document.createElement('script');
    script.src = 'https://api.dmcdn.net/all.js';
    script.onload = callback;
    document.head.appendChild(script);
  }

  function loadVideo(index) {
    if (index < 0 || index >= videoSets.length) return;

    const oldWrapper = document.getElementById('videoWrapper');
    if (oldWrapper) container.removeChild(oldWrapper);

    const wrapper = document.createElement('div');
    wrapper.id = 'videoWrapper';
    wrapper.style.display = 'flex';
    wrapper.style.alignItems = 'center';
    wrapper.style.justifyContent = 'center';
    wrapper.style.width = '100%';
    wrapper.style.height = '100vh';
    wrapper.style.gap = '10px';

    const leftBtn = document.createElement('button');
    leftBtn.textContent = '❮';
    leftBtn.style.fontSize = '2rem';
    leftBtn.style.cursor = 'pointer';
    leftBtn.style.backgroundColor = '#222';
    leftBtn.style.color = '#fff';
    leftBtn.onmouseover = () => leftBtn.style.backgroundColor = '#444';
    leftBtn.onmouseout = () => leftBtn.style.backgroundColor = '#222';

    const rightBtn = document.createElement('button');
    rightBtn.textContent = '❯';
    rightBtn.style.fontSize = '2rem';
    rightBtn.style.cursor = 'pointer';
    rightBtn.style.backgroundColor = '#222';
    rightBtn.style.color = '#fff';
    rightBtn.onmouseover = () => rightBtn.style.backgroundColor = '#444';
    rightBtn.onmouseout = () => rightBtn.style.backgroundColor = '#222';

    const iframe = document.createElement('iframe');
    iframe.id = 'videoPlayer';
    iframe.width = '100%';
    iframe.height = '100%';
    iframe.frameBorder = '0';
    iframe.allow =
      'autoplay; fullscreen; picture-in-picture';
    iframe.allowFullscreen = true;
    iframe.src = `https://www.dailymotion.com/embed/video/${videoSets[index].video}?autoplay=1`;

    wrapper.appendChild(leftBtn);
    wrapper.appendChild(iframe);
    wrapper.appendChild(rightBtn);
    container.appendChild(wrapper);

    currentIndex = index;

    iframe.onload = () => {
      const player = DM.player(iframe);

      player.addEventListener('end', () => {
        loadVideo(currentIndex + 1);
      });
    };

    leftBtn.addEventListener('click', () => {
      loadVideo(currentIndex - 1);
    });

    rightBtn.addEventListener('click', () => {
      loadVideo(currentIndex + 1);
    });
  }

  loadDMAPI(() => {
    loadVideo(currentIndex);
  });
});
