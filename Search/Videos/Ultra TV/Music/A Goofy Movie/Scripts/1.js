document.addEventListener('DOMContentLoaded', () => {
  const container = document.body;

  const videoSets = [
        { video: 'https://player.vimeo.com/video/1044320971' },  
        { video: 'https://player.vimeo.com/video/1044322863' },  
        { video: 'https://player.vimeo.com/video/1044320655' }
  ];

  let currentIndex = 0;

  function loadVimeoAPI(callback) {
    const script = document.createElement('script');
    script.src = 'https://player.vimeo.com/api/player.js';
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
    leftBtn.className = 'demo w3-opacity w3-hover-opacity-off';
    leftBtn.style.fontSize = '2rem';
    leftBtn.style.cursor = 'pointer';
    leftBtn.style.backgroundColor = '#222';   // dark background
    leftBtn.style.color = '#fff';   
    leftBtn.onmouseover = () => leftBtn.style.backgroundColor = '#444';
    leftBtn.onmouseout = () => leftBtn.style.backgroundColor = '#222';


    const rightBtn = document.createElement('button');
    rightBtn.textContent = '❯';
    rightBtn.className = 'demo w3-opacity w3-hover-opacity-off';
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
      'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
    iframe.allowFullscreen = true;
    iframe.src = videoSets[index].video + '?autoplay=1';

    wrapper.appendChild(leftBtn);
    wrapper.appendChild(iframe);
    wrapper.appendChild(rightBtn);
    container.appendChild(wrapper);

    currentIndex = index;

    iframe.onload = () => {
      const player = new Vimeo.Player(iframe);
      player.on('ended', () => {
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


  loadVimeoAPI(() => {
    loadVideo(currentIndex);
  });
});