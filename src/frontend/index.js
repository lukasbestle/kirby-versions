// Vue components
import Changes from "./components/Misc/Changes.vue";
import CreateErrorDialog from "./components/Dialogs/CreateErrorDialog.vue";
import CreateDialog from "./components/Dialogs/CreateDialog.vue";
import DeleteDialog from "./components/Dialogs/DeleteDialog.vue";
import DeployDialog from "./components/Dialogs/DeployDialog.vue";
import ExportDialog from "./components/Dialogs/ExportDialog.vue";
import InstanceName from "./components/Misc/InstanceName.vue";
import Status from "./components/Status.vue";
import Version from "./components/Misc/Version.vue";
import Versions from "./components/Versions.vue";
import View from "./components/View.vue";

// Vuex store
import Store from "./store.js";

panel.plugin("lukasbestle/versions", {
  components: {
    "lbvs-changes": Changes,
    "lbvs-create-error-dialog": CreateErrorDialog,
    "lbvs-create-dialog": CreateDialog,
    "lbvs-delete-dialog": DeleteDialog,
    "lbvs-deploy-dialog": DeployDialog,
    "lbvs-export-dialog": ExportDialog,
    "lbvs-instance-name": InstanceName,
    "lbvs-status": Status,
    "lbvs-version": Version,
    "lbvs-versions": Versions
  },
  views: {
    versions: {
      component: View,
      icon: "layers",
      menu(app) {
        if (app.$permissions["lukasbestle.versions"].access !== true) {
          return "disabled";
        }

        return true;
      }
    }
  },
  created(app) {
    app.$store.registerModule("versions", Store(app));
  }
});
