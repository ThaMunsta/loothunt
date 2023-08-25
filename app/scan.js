var app = new Vue({
  el: '#app',
  data: {
    scanner: null,
    activeCameraId: null,
    cameras: [],
    scans: []
  },
  mounted: function () {
    var self = this;
    self.scanner = new Instascan.Scanner({ video: document.getElementById('preview'), scanPeriod: 5, refractoryPeriod: 1000, mirror: false});
    self.scanner.addListener('scan', function (content, image) {
    	if (content.indexOf("loothunt.ca") > -1){
    		window.location.replace(content);
    	}
    	else self.scans.unshift({ date: +(Date.now()), content: "Not a Loot Hunt code!" });
    });
    Instascan.Camera.getCameras().then(function (cameras) {
      self.cameras = cameras;
      if (cameras.length > 1) {
        self.activeCameraId = cameras[1].id;
        self.scanner.start(cameras[1]);
      } 
      else if (cameras.length > 0) {
        self.activeCameraId = cameras[0].id;
        self.scanner.start(cameras[0]);
      }
      else {
        console.error('No cameras found.');
      }
    }).catch(function (e) {
      console.error(e);
    });
  },
  methods: {
    formatName: function (name) {
      return name || '(unknown)';
    },
    selectCamera: function (camera) {
      this.activeCameraId = camera.id;
      this.scanner.start(camera);
    }
  }
});
