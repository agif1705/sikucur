<style>
  html,
  body {
    height: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
    overflow: hidden !important;
  }

  .navbar {
    flex-shrink: 0;
  }

  main {
    min-height: 0;
    flex: 1;
  }

  .col-8 .card {
    height: 100% !important;
  }

  .col-8 .card-body {
    flex: 1 !important;
    min-height: 0 !important;
  }

  .gallery-container {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 300px;
    max-height: 400px;
  }

  #galleryCarousel {
    flex: 1;
    width: 100%;
    height: 100%;
    min-height: 250px;
  }

  .carousel-inner,
  .carousel-item {
    height: 100%;
  }

  .carousel-inner {
    border-radius: 4px;
    overflow: hidden;
  }

  .carousel-gallery-img {
    width: 100% !important;
    height: 100% !important;
    object-fit: contain !important;
    background: #f8f9fa;
    transition: transform 0.3s ease;
    border-radius: 4px;
  }

  .no-gallery {
    width: 100%;
    height: 200px;
    background: #f8f9fa;
    border-radius: 4px;
  }

  .carousel-control-prev,
  .carousel-control-next {
    width: 8%;
    opacity: 0.7;
    transition: opacity 0.3s ease;
  }

  .carousel-control-prev:hover,
  .carousel-control-next:hover {
    opacity: 1;
  }

  .carousel-control-prev-icon,
  .carousel-control-next-icon {
    background-size: 16px 16px;
  }

  .carousel-indicators {
    margin-bottom: 0.5rem;
  }

  .carousel-indicators [data-bs-target] {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin: 0 3px;
    background-color: rgba(255, 255, 255, 0.5);
    border: none;
  }

  .carousel-indicators .active {
    background-color: rgba(255, 255, 255, 1);
    width: 10px;
    height: 10px;
  }

  .carousel-item:hover .carousel-gallery-img {
    transform: scale(1.02);
  }

  .carousel-item {
    transition: transform 0.6s ease-in-out;
  }

  @keyframes speechFade {
    0% {
      opacity: 0;
      transform: translateX(-15%) scale(0.8);
    }

    25%,
    75% {
      opacity: 1;
      transform: translateX(-15%) scale(1);
    }

    100% {
      opacity: 0;
      transform: translateX(-15%) scale(0.8);
    }
  }

  .speech-bubble {
    animation: speechFade 6s ease-in-out infinite;
  }
</style>
