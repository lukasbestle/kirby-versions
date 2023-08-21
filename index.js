(function() {
  "use strict";
  const Changes_vue_vue_type_style_index_0_lang = "";
  function normalizeComponent(scriptExports, render, staticRenderFns, functionalTemplate, injectStyles, scopeId, moduleIdentifier, shadowMode) {
    var options = typeof scriptExports === "function" ? scriptExports.options : scriptExports;
    if (render) {
      options.render = render;
      options.staticRenderFns = staticRenderFns;
      options._compiled = true;
    }
    if (functionalTemplate) {
      options.functional = true;
    }
    if (scopeId) {
      options._scopeId = "data-v-" + scopeId;
    }
    var hook;
    if (moduleIdentifier) {
      hook = function(context) {
        context = context || // cached call
        this.$vnode && this.$vnode.ssrContext || // stateful
        this.parent && this.parent.$vnode && this.parent.$vnode.ssrContext;
        if (!context && typeof __VUE_SSR_CONTEXT__ !== "undefined") {
          context = __VUE_SSR_CONTEXT__;
        }
        if (injectStyles) {
          injectStyles.call(this, context);
        }
        if (context && context._registeredComponents) {
          context._registeredComponents.add(moduleIdentifier);
        }
      };
      options._ssrRegister = hook;
    } else if (injectStyles) {
      hook = shadowMode ? function() {
        injectStyles.call(
          this,
          (options.functional ? this.parent : this).$root.$options.shadowRoot
        );
      } : injectStyles;
    }
    if (hook) {
      if (options.functional) {
        options._injectStyles = hook;
        var originalRender = options.render;
        options.render = function renderWithStyleInjection(h, context) {
          hook.call(context);
          return originalRender(h, context);
        };
      } else {
        var existing = options.beforeCreate;
        options.beforeCreate = existing ? [].concat(existing, hook) : [hook];
      }
    }
    return {
      exports: scriptExports,
      options
    };
  }
  const _sfc_main$d = {
    props: {
      changes: Object
    },
    computed: {
      markedChangesForStaging() {
        return this.$store.state.versions.data.markedChangesForStaging;
      }
    },
    mounted() {
      for (const change in this.changes) {
        this.$store.dispatch("versions/setChanges", {
          path: change,
          status: this.changes[change]
        });
      }
    },
    methods: {
      updateChange(path, state, checked) {
        this.$store.dispatch("versions/setChanges", {
          path,
          status: checked ? state : false
        });
      }
    }
  };
  var _sfc_render$d = function render() {
    var _vm = this, _c = _vm._self._c;
    return _c("ul", { staticClass: "lbvs-changes" }, _vm._l(_vm.changes, function(status, path) {
      return _c("li", { key: path }, [_c("div", [_c("k-input", { attrs: { "id": path, "name": path, "value": status, "checked": status !== false, "type": "checkbox" }, on: { "input": ($event) => _vm.updateChange(path, status, $event) } })], 1), _c("span", { attrs: { "data-status": status, "title": _vm.$t("versions.label.status." + status) } }, [_vm._v(" " + _vm._s(status) + " ")]), _c("label", { attrs: { "for": path } }, [_vm._v(_vm._s(path))])]);
    }), 0);
  };
  var _sfc_staticRenderFns$d = [];
  _sfc_render$d._withStripped = true;
  var __component__$d = /* @__PURE__ */ normalizeComponent(
    _sfc_main$d,
    _sfc_render$d,
    _sfc_staticRenderFns$d,
    false,
    null,
    null,
    null,
    null
  );
  __component__$d.options.__file = "/Users/marcel/Code/packages/kirby-versions/src/frontend/components/Misc/Changes.vue";
  const Changes = __component__$d.exports;
  const Stages_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$c = {
    props: {
      toStage: Object
    }
  };
  var _sfc_render$c = function render() {
    var _vm = this, _c = _vm._self._c;
    return _c("ul", { staticClass: "lbvs-changes" }, _vm._l(_vm.toStage, function(status, path) {
      return _c("li", { key: path }, [_c("span", { attrs: { "data-status": status, "title": _vm.$t("versions.label.status." + status) } }, [_vm._v(" " + _vm._s(status) + " ")]), _vm._v(" " + _vm._s(path) + " ")]);
    }), 0);
  };
  var _sfc_staticRenderFns$c = [];
  _sfc_render$c._withStripped = true;
  var __component__$c = /* @__PURE__ */ normalizeComponent(
    _sfc_main$c,
    _sfc_render$c,
    _sfc_staticRenderFns$c,
    false,
    null,
    null,
    null,
    null
  );
  __component__$c.options.__file = "/Users/marcel/Code/packages/kirby-versions/src/frontend/components/Misc/Stages.vue";
  const Stages = __component__$c.exports;
  const CreateErrorDialog_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$b = {
    data() {
      return {
        error: {
          message: null,
          details: {
            lockedModels: {}
          }
        }
      };
    },
    methods: {
      open(error) {
        this.error = error;
        this.$refs.dialog.open();
      }
    }
  };
  var _sfc_render$b = function render() {
    var _vm = this, _c = _vm._self._c;
    return _c("k-dialog", { ref: "dialog", staticClass: "lbvs-create-error-dialog", attrs: { "cancel-button": _vm.$t("close"), "submit-button": false } }, [_c("p", { staticClass: "lbvs-create-error-dialog-message" }, [_vm._v(" " + _vm._s(_vm.error.message) + " ")]), _c("ul", { staticClass: "lbvs-create-error-dialog-list" }, _vm._l(_vm.error.details.lockedModels, function(users, model) {
      return _c("li", { key: model }, [_vm._v(" " + _vm._s(model) + " "), _c("span", [_vm._v("(" + _vm._s(users.join(", ")) + ")")])]);
    }), 0)]);
  };
  var _sfc_staticRenderFns$b = [];
  _sfc_render$b._withStripped = true;
  var __component__$b = /* @__PURE__ */ normalizeComponent(
    _sfc_main$b,
    _sfc_render$b,
    _sfc_staticRenderFns$b,
    false,
    null,
    null,
    null,
    null
  );
  __component__$b.options.__file = "/Users/marcel/Code/packages/kirby-versions/src/frontend/components/Dialogs/CreateErrorDialog.vue";
  const CreateErrorDialog = __component__$b.exports;
  const CreateDialog_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$a = {
    data() {
      return {
        instance: null,
        inProgress: false,
        toStage: {}
      };
    },
    computed: {
      fields() {
        return {
          label: {
            autofocus: true,
            icon: "title",
            label: this.$t("versions.label.label"),
            type: "text"
          }
        };
      }
    },
    methods: {
      async onSubmit() {
        if (this.inProgress === true) {
          return;
        }
        try {
          this.inProgress = true;
          let label = this.$refs.form.value.label;
          if (!label) {
            throw this.$t("field.required");
          }
          await this.$store.dispatch({
            type: "versions/addChangesToStage",
            changes: this.toStage
          });
          await this.$store.dispatch({
            type: "versions/createVersion",
            instance: this.instance,
            label
          });
          this.$store.dispatch("notification/success", ":)");
          this.$refs.dialog.close();
        } catch (e) {
          this.$refs.dialog.error(e.message || e);
        } finally {
          this.inProgress = false;
        }
      },
      open(instance, toStage) {
        this.instance = instance;
        this.toStage = toStage;
        this.$refs.dialog.open();
      }
    }
  };
  var _sfc_render$a = function render() {
    var _vm = this, _c = _vm._self._c;
    return _c("div", [_c("k-dialog", { ref: "dialog", attrs: { "size": "large", "submit-button": _vm.$t("versions.button.create"), "theme": "positive" }, on: { "submit": _vm.onSubmit } }, [_c("k-form", { ref: "form", attrs: { "fields": _vm.fields }, on: { "submit": _vm.onSubmit }, scopedSlots: _vm._u([{ key: "header", fn: function() {
      return [_c("k-field", { staticClass: "lbvs-create-changes", attrs: { "label": _vm.$t("versions.label.changes") } }, [_c("lbvs-stages", { attrs: { "to-stage": _vm.toStage } })], 1)];
    }, proxy: true }]) })], 1), _c("lbvs-create-error-dialog", { ref: "errorDialog" })], 1);
  };
  var _sfc_staticRenderFns$a = [];
  _sfc_render$a._withStripped = true;
  var __component__$a = /* @__PURE__ */ normalizeComponent(
    _sfc_main$a,
    _sfc_render$a,
    _sfc_staticRenderFns$a,
    false,
    null,
    null,
    null,
    null
  );
  __component__$a.options.__file = "/Users/marcel/Code/packages/kirby-versions/src/frontend/components/Dialogs/CreateDialog.vue";
  const CreateDialog = __component__$a.exports;
  const DeleteDialog_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$9 = {
    data() {
      return {
        inProgress: false,
        version: null
      };
    },
    methods: {
      async onSubmit() {
        if (this.inProgress === true) {
          return;
        }
        try {
          this.inProgress = true;
          await this.$store.dispatch({
            type: "versions/deleteVersion",
            version: this.version.name
          });
          this.$store.dispatch("notification/success", ":)");
          this.$refs.dialog.close();
        } catch (e) {
          this.$refs.dialog.error(e.message || e);
        } finally {
          this.inProgress = false;
        }
      },
      open(version) {
        this.version = version;
        this.$refs.dialog.open();
      }
    }
  };
  var _sfc_render$9 = function render() {
    var _vm = this, _c = _vm._self._c;
    return _c("k-dialog", { ref: "dialog", staticClass: "lbvs-version-dialog lbvs-version-delete-dialog", attrs: { "icon": "trash", "submit-button": _vm.$t("versions.button.delete"), "theme": "negative" }, on: { "submit": _vm.onSubmit } }, [_vm.version ? _c("lbvs-version", { attrs: { "version": _vm.version } }) : _vm._e(), _c("p", [_vm._v(_vm._s(_vm.$t("versions.message.delete")))])], 1);
  };
  var _sfc_staticRenderFns$9 = [];
  _sfc_render$9._withStripped = true;
  var __component__$9 = /* @__PURE__ */ normalizeComponent(
    _sfc_main$9,
    _sfc_render$9,
    _sfc_staticRenderFns$9,
    false,
    null,
    null,
    null,
    null
  );
  __component__$9.options.__file = "/Users/marcel/Code/packages/kirby-versions/src/frontend/components/Dialogs/DeleteDialog.vue";
  const DeleteDialog = __component__$9.exports;
  const _sfc_main$8 = {
    data() {
      return {
        inProgress: false,
        version: null
      };
    },
    computed: {
      fields() {
        let currentInstance = this.$store.getters["versions/currentInstance"];
        let options = Object.values(
          this.$store.state.versions.data.instances
        ).map((instance) => ({ text: instance.name, value: instance.name }));
        return {
          instance: {
            disabled: options.length <= 1,
            empty: false,
            icon: "box",
            label: this.$t("versions.label.targetInstance"),
            options,
            placeholder: currentInstance.name,
            type: "select",
            value: currentInstance.name
          }
        };
      }
    },
    methods: {
      async onSubmit() {
        if (this.inProgress === true) {
          return;
        }
        try {
          this.inProgress = true;
          let instance = this.$refs.form.value.instance || this.$store.getters["versions/currentInstance"].name;
          await this.$store.dispatch({
            type: "versions/deployVersion",
            version: this.version.name,
            instance
          });
          this.$store.dispatch("notification/success", ":)");
          this.$refs.dialog.close();
        } catch (e) {
          this.$refs.dialog.error(e.message || e);
        } finally {
          this.inProgress = false;
        }
      },
      open(version) {
        this.version = version;
        this.$refs.dialog.open();
      }
    }
  };
  var _sfc_render$8 = function render() {
    var _vm = this, _c = _vm._self._c;
    return _c("k-dialog", { ref: "dialog", staticClass: "lbvs-version-dialog", attrs: { "submit-button": _vm.$t("versions.button.deploy"), "theme": "positive" }, on: { "submit": _vm.onSubmit } }, [_vm.version ? _c("lbvs-version", { attrs: { "version": _vm.version } }) : _vm._e(), _c("k-form", { ref: "form", attrs: { "fields": _vm.fields }, on: { "submit": _vm.onSubmit } })], 1);
  };
  var _sfc_staticRenderFns$8 = [];
  _sfc_render$8._withStripped = true;
  var __component__$8 = /* @__PURE__ */ normalizeComponent(
    _sfc_main$8,
    _sfc_render$8,
    _sfc_staticRenderFns$8,
    false,
    null,
    null,
    null,
    null
  );
  __component__$8.options.__file = "/Users/marcel/Code/packages/kirby-versions/src/frontend/components/Dialogs/DeployDialog.vue";
  const DeployDialog = __component__$8.exports;
  const _sfc_main$7 = {
    data() {
      return {
        data: null,
        version: {}
      };
    },
    computed: {
      details() {
        if (this.data) {
          return [
            {
              title: this.$t("versions.label.fileSize"),
              value: this.data.filesize
            }
          ];
        }
        return [];
      },
      supportsClipboard() {
        try {
          window.navigator.clipboard.writeText;
          return true;
        } catch (e) {
          return false;
        }
      }
    },
    methods: {
      async copyToClipboard() {
        await window.navigator.clipboard.writeText(this.data.url);
        this.$store.dispatch("notification/success", ":)");
      },
      download() {
        window.location = this.data.url;
        this.$store.dispatch("notification/success", ":)");
      },
      async open(version) {
        this.data = null;
        this.version = version;
        this.$refs.dialog.open();
        let data = await this.$store.dispatch({
          type: "versions/exportVersion",
          version: this.version.name
        });
        if (version === this.version) {
          this.data = data;
        }
      }
    }
  };
  var _sfc_render$7 = function render() {
    var _vm = this, _c = _vm._self._c;
    return _c("k-dialog", { ref: "dialog", staticClass: "lbvs-version-dialog", attrs: { "cancel-button": _vm.$t(_vm.data ? "close" : "cancel"), "submit-button": false } }, [_vm.version ? _c("lbvs-version", { attrs: { "details": _vm.details, "version": _vm.version } }) : _vm._e(), !_vm.data ? _c("p", [_vm._v(" " + _vm._s(_vm.$t("versions.message.exporting")) + " ")]) : _c("k-button-group", [_c("k-button", { attrs: { "icon": "download" }, on: { "click": _vm.download } }, [_vm._v(" " + _vm._s(_vm.$t("versions.button.download")) + " ")]), _c("k-button", { attrs: { "icon": "copy", "disabled": !_vm.supportsClipboard }, on: { "click": _vm.copyToClipboard } }, [_vm._v(" " + _vm._s(_vm.$t("versions.button.copyLink")) + " ")])], 1)], 1);
  };
  var _sfc_staticRenderFns$7 = [];
  _sfc_render$7._withStripped = true;
  var __component__$7 = /* @__PURE__ */ normalizeComponent(
    _sfc_main$7,
    _sfc_render$7,
    _sfc_staticRenderFns$7,
    false,
    null,
    null,
    null,
    null
  );
  __component__$7.options.__file = "/Users/marcel/Code/packages/kirby-versions/src/frontend/components/Dialogs/ExportDialog.vue";
  const ExportDialog = __component__$7.exports;
  const InstanceName_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$6 = {
    props: {
      inline: Boolean,
      // support both a full instance object or just the name string
      instance: [Object, String]
    },
    computed: {
      element() {
        return this.inline ? "span" : "strong";
      },
      instanceObj() {
        if (typeof this.instance === "string") {
          return { name: this.instance, color: "var(--color-gray-300)" };
        }
        return this.instance;
      }
    }
  };
  var _sfc_render$6 = function render() {
    var _vm = this, _c = _vm._self._c;
    return _c(_vm.element, { tag: "component", staticClass: "lbvs-instance-name", style: { backgroundColor: _vm.instanceObj.color } }, [_vm._v(" " + _vm._s(_vm.instanceObj.name) + " ")]);
  };
  var _sfc_staticRenderFns$6 = [];
  _sfc_render$6._withStripped = true;
  var __component__$6 = /* @__PURE__ */ normalizeComponent(
    _sfc_main$6,
    _sfc_render$6,
    _sfc_staticRenderFns$6,
    false,
    null,
    null,
    null,
    null
  );
  __component__$6.options.__file = "/Users/marcel/Code/packages/kirby-versions/src/frontend/components/Misc/InstanceName.vue";
  const InstanceName = __component__$6.exports;
  const InstanceNames_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$5 = {
    props: {
      // support both a list of instances or a single instance
      value: [Array, String]
    },
    computed: {
      instances() {
        return Array.isArray(this.value) ? this.value : [this.value];
      }
    }
  };
  var _sfc_render$5 = function render() {
    var _vm = this, _c = _vm._self._c;
    return _c("div", { staticClass: "lbvs-instance-names-cell" }, _vm._l(_vm.instances, function(instance) {
      return _c("lbvs-instance-name", { key: instance, attrs: { "inline": true, "instance": _vm.$store.state.versions.data.instances[instance] || instance } });
    }), 1);
  };
  var _sfc_staticRenderFns$5 = [];
  _sfc_render$5._withStripped = true;
  var __component__$5 = /* @__PURE__ */ normalizeComponent(
    _sfc_main$5,
    _sfc_render$5,
    _sfc_staticRenderFns$5,
    false,
    null,
    null,
    null,
    null
  );
  __component__$5.options.__file = "/Users/marcel/Code/packages/kirby-versions/src/frontend/components/Cells/InstanceNames.vue";
  const InstanceNamesCell = __component__$5.exports;
  const Status_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$4 = {
    computed: {
      canCreateVersion() {
        return this.$permissions["lukasbestle.versions"].create === true && Object.keys(this.currentChanges).length > 0;
      },
      currentChanges() {
        return this.$store.getters["versions/currentInstance"].changes;
      }
    },
    methods: {
      onCreate() {
        let instance = this.$store.getters["versions/currentInstance"].name;
        const toStage = this.$store.getters["versions/currentChanges"];
        return this.$refs.createDialog.open(instance, toStage);
      }
    }
  };
  var _sfc_render$4 = function render() {
    var _vm = this, _c = _vm._self._c;
    return _c("div", { staticClass: "lbvs-status" }, [_c("k-view", [_c("k-grid", [_c("k-column", { attrs: { "width": "1/3" } }, [_c("header", { staticClass: "k-section-header" }, [_c("k-headline", [_vm._v(" " + _vm._s(_vm.$t("versions.label.instances")) + " ")])], 1), _c("ul", { staticClass: "lbvs-status-instances" }, _vm._l(_vm.$store.state.versions.data.instances, function(instance) {
      return _c("li", { key: instance.name, class: { current: instance.isCurrent } }, [_c("lbvs-instance-name", { attrs: { "instance": instance } }), instance.isCurrent ? _c("span", { staticClass: "lbvs-status-current" }, [_vm._v(" " + _vm._s(_vm.$t("versions.label.current")) + " ")]) : _vm._e(), instance.version ? _c("lbvs-version", { attrs: { "version": {
        name: instance.version,
        label: instance.versionLabel
      } } }) : _vm._e()], 1);
    }), 0)]), _c("k-column", { staticClass: "lbvs-status-changes", attrs: { "width": "2/3" } }, [_c("header", { staticClass: "k-section-header" }, [_c("k-headline", [_vm._v(" " + _vm._s(_vm.$t("versions.label.changes")) + " ")]), _c("k-button-group", [_c("k-button", { attrs: { "icon": "add", "disabled": _vm.canCreateVersion === false }, on: { "click": _vm.onCreate } }, [_vm._v(" " + _vm._s(_vm.$t("versions.button.create")) + " ")])], 1)], 1), _c("lbvs-changes", { attrs: { "changes": _vm.currentChanges } })], 1)], 1)], 1), _c("lbvs-create-dialog", { ref: "createDialog" })], 1);
  };
  var _sfc_staticRenderFns$4 = [];
  _sfc_render$4._withStripped = true;
  var __component__$4 = /* @__PURE__ */ normalizeComponent(
    _sfc_main$4,
    _sfc_render$4,
    _sfc_staticRenderFns$4,
    false,
    null,
    null,
    null,
    null
  );
  __component__$4.options.__file = "/Users/marcel/Code/packages/kirby-versions/src/frontend/components/Status.vue";
  const Status = __component__$4.exports;
  const Version_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$3 = {
    props: {
      details: {
        type: Array,
        default() {
          return [];
        }
      },
      instances: Boolean,
      version: Object
    },
    computed: {
      mergedDetails() {
        let details = [
          {
            title: this.$t("versions.label.versionName"),
            value: this.version.name
          },
          ...this.details
        ];
        return details.filter((detail) => detail.value);
      }
    }
  };
  var _sfc_render$3 = function render() {
    var _vm = this, _c = _vm._self._c;
    return _c("div", { staticClass: "lbvs-version" }, [_c("strong", [_vm._v(_vm._s(_vm.version.label))]), _c("dl", { staticClass: "lbvs-version-details" }, [_vm._l(_vm.mergedDetails, function(detail) {
      return [_c("dt", { key: detail.title, staticClass: "k-offscreen" }, [_vm._v(_vm._s(detail.title) + ":")]), _c("dd", { key: detail.title, attrs: { "title": detail.title } }, [_vm._v(" " + _vm._s(detail.value) + " ")])];
    })], 2)]);
  };
  var _sfc_staticRenderFns$3 = [];
  _sfc_render$3._withStripped = true;
  var __component__$3 = /* @__PURE__ */ normalizeComponent(
    _sfc_main$3,
    _sfc_render$3,
    _sfc_staticRenderFns$3,
    false,
    null,
    null,
    null,
    null
  );
  __component__$3.options.__file = "/Users/marcel/Code/packages/kirby-versions/src/frontend/components/Misc/Version.vue";
  const Version = __component__$3.exports;
  const VersionLabel_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$2 = {
    props: {
      value: Object
    }
  };
  var _sfc_render$2 = function render() {
    var _vm = this, _c = _vm._self._c;
    return _c("div", { staticClass: "lbvs-version-label-cell" }, [_c("lbvs-version", { attrs: { "version": _vm.value } })], 1);
  };
  var _sfc_staticRenderFns$2 = [];
  _sfc_render$2._withStripped = true;
  var __component__$2 = /* @__PURE__ */ normalizeComponent(
    _sfc_main$2,
    _sfc_render$2,
    _sfc_staticRenderFns$2,
    false,
    null,
    null,
    null,
    null
  );
  __component__$2.options.__file = "/Users/marcel/Code/packages/kirby-versions/src/frontend/components/Cells/VersionLabel.vue";
  const VersionLabelCell = __component__$2.exports;
  const Versions_vue_vue_type_style_index_0_lang = "";
  const _sfc_main$1 = {
    computed: {
      columns() {
        return {
          title: {
            label: this.$t("versions.label.label"),
            type: "lbvs-version-label",
            mobile: true,
            width: "35%"
          },
          instances: {
            label: this.$t("versions.label.instances"),
            type: "lbvs-instance-names",
            mobile: true,
            width: "25%"
          },
          creation: {
            label: this.$t("versions.label.creation"),
            type: "text",
            width: "25%"
          },
          originInstance: {
            label: this.$t("versions.label.originInstance"),
            type: "lbvs-instance-names",
            width: "15%"
          }
        };
      },
      items() {
        return Object.values(this.$store.state.versions.data.versions).map(
          (version) => {
            version.creation = this.$t("versions.label.creationData", {
              created: version.created ? this.$library.dayjs.unix(version.created).format("YYYY-MM-DD HH:mm") : "?",
              creator: version.creatorName || version.creatorEmail || "?"
            });
            version.title = {
              label: version.label,
              name: version.name
            };
            version.options = this.options(version);
            return version;
          }
        );
      }
    },
    methods: {
      onOption(option, version) {
        return this.$refs[option + "Dialog"].open(version);
      },
      options(version) {
        let permissions = this.$permissions["lukasbestle.versions"];
        return [
          {
            click: "export",
            disabled: permissions.export !== true,
            icon: "download",
            text: this.$t("versions.button.export")
          },
          {
            click: "deploy",
            disabled: permissions.deploy !== true,
            icon: "wand",
            text: this.$t("versions.button.deploy")
          },
          {
            click: "delete",
            disabled: permissions.delete !== true || version.instances.length > 0,
            icon: "trash",
            text: this.$t("versions.button.delete")
          }
        ];
      }
    }
  };
  var _sfc_render$1 = function render() {
    var _vm = this, _c = _vm._self._c;
    return _c("div", { staticClass: "lbvs-versions" }, [_c("k-view", [_c("header", { staticClass: "k-section-header" }, [_c("k-headline", [_vm._v(" " + _vm._s(_vm.$t("versions.label.versions")) + " ")])], 1), _vm.items.length ? _c("k-items", { attrs: { "columns": _vm.columns, "items": _vm.items, "layout": "table", "sortable": false }, on: { "option": _vm.onOption } }) : _c("k-empty", { attrs: { "icon": "protected", "layout": "table" } }, [_vm._v(" " + _vm._s(_vm.$t("versions.label.empty")) + " ")])], 1), _c("lbvs-export-dialog", { ref: "exportDialog" }), _c("lbvs-deploy-dialog", { ref: "deployDialog" }), _c("lbvs-delete-dialog", { ref: "deleteDialog" })], 1);
  };
  var _sfc_staticRenderFns$1 = [];
  _sfc_render$1._withStripped = true;
  var __component__$1 = /* @__PURE__ */ normalizeComponent(
    _sfc_main$1,
    _sfc_render$1,
    _sfc_staticRenderFns$1,
    false,
    null,
    null,
    null,
    null
  );
  __component__$1.options.__file = "/Users/marcel/Code/packages/kirby-versions/src/frontend/components/Versions.vue";
  const Versions = __component__$1.exports;
  const View_vue_vue_type_style_index_0_lang = "";
  const _sfc_main = {
    data() {
      return {
        isLoading: true
      };
    },
    async mounted() {
      if (this.$permissions["lukasbestle.versions"].access !== true) {
        this.$go("/");
      }
      try {
        this.isLoading = true;
        await this.$store.dispatch("versions/load");
      } finally {
        this.isLoading = false;
      }
    }
  };
  var _sfc_render = function render() {
    var _vm = this, _c = _vm._self._c;
    return _c("k-inside", [_c("div", { staticClass: "lbvs-view" }, [_vm.isLoading ? _c("k-loader") : [_c("lbvs-status"), _c("lbvs-versions")]], 2)]);
  };
  var _sfc_staticRenderFns = [];
  _sfc_render._withStripped = true;
  var __component__ = /* @__PURE__ */ normalizeComponent(
    _sfc_main,
    _sfc_render,
    _sfc_staticRenderFns,
    false,
    null,
    null,
    null,
    null
  );
  __component__.options.__file = "/Users/marcel/Code/packages/kirby-versions/src/frontend/components/View.vue";
  const View = __component__.exports;
  const Store = (app) => ({
    namespaced: true,
    state: {
      data: {
        markedChangesForStaging: {},
        instances: {},
        versions: {}
      }
    },
    getters: {
      /**
       * @returns {object} The instance marked with `isCurrent: true`
       */
      currentInstance(state) {
        return Object.values(state.data.instances).find(
          (instance) => instance.isCurrent
        );
      },
      currentChanges(state) {
        return state.data.markedChangesForStaging;
      }
    },
    mutations: {
      SET_DATA(state, { instances, versions }) {
        state.data.instances = instances;
        state.data.versions = versions;
      },
      SET_CHANGES(state, { path, status }) {
        state.data.markedChangesForStaging[path] = status;
      },
      REMOVE_CHANGES(state, { path }) {
        delete state.data.markedChangesForStaging[path];
      }
    },
    actions: {
      /**
       * Initialize the plugin data from the API
       */
      async load({ commit }) {
        commit("SET_DATA", await app.$api.get("versions"));
      },
      /**
       * Prepare version creation
       * Stages all changes and validates that the version
       * can be created based on the current set of changes
       *
       * @param {string} instance Name of the instance to create the version from
       * @returns {object} List of staged changes
       */
      async prepareVersionCreation(context, { instance }) {
        return await app.$api.post("versions/prepareCreation", { instance });
      },
      /**
       * Stage changes
       * Stage changes for version creation
       *
       * @param {object} changes List of changes to stage
       */
      async addChangesToStage({ commit }, { changes }) {
        await app.$api.post("versions/addChanges", { changes });
      },
      /**
       * Create version
       * Commits the previously prepared version
       *
       * @param {string} instance Name of the instance to create the version from
       * @param {string} label Custom version label
       */
      async createVersion({ commit }, { instance, label }) {
        let data = await app.$api.post("versions/create", { instance, label });
        commit("SET_DATA", data);
      },
      /**
       * Delete version
       * Deletes a version's Git tag
       *
       * @param {string} version Unique version name
       */
      async deleteVersion({ commit }, { version }) {
        let data = await app.$api.delete("versions/versions/" + version);
        commit("SET_DATA", data);
      },
      /**
       * Deploy version
       * Deploys a version to a specified instance
       *
       * @param {string} instance Name of the instance to deploy to
       * @param {string} version Unique version name
       */
      async deployVersion({ commit }, { instance, version }) {
        let data = await app.$api.post(
          "versions/versions/" + version + "/deploy",
          { instance }
        );
        commit("SET_DATA", data);
      },
      /**
       * Export version
       * Returns the URL to a ZIP file of the given version
       *
       * @param {string} version Unique version name
       * @returns {object} ZIP `url`, version `name` and `label`, `filesize`
       */
      async exportVersion(context, { version }) {
        return await app.$api.post("versions/versions/" + version + "/export");
      },
      setChanges({ commit }, { path, status }) {
        if (status === false) {
          commit("REMOVE_CHANGES", { path });
          return;
        }
        return commit("SET_CHANGES", { path, status });
      }
    }
  });
  panel.plugin("lukasbestle/versions", {
    components: {
      "k-table-lbvs-instance-names-cell": InstanceNamesCell,
      "k-table-lbvs-version-label-cell": VersionLabelCell,
      "lbvs-changes": Changes,
      "lbvs-stages": Stages,
      "lbvs-create-error-dialog": CreateErrorDialog,
      "lbvs-create-dialog": CreateDialog,
      "lbvs-delete-dialog": DeleteDialog,
      "lbvs-deploy-dialog": DeployDialog,
      "lbvs-export-dialog": ExportDialog,
      "lbvs-instance-name": InstanceName,
      "lbvs-status": Status,
      "lbvs-version": Version,
      "lbvs-versions": Versions,
      "lbvs-view": View
    },
    created(app) {
      app.$store.registerModule("versions", Store(app));
    }
  });
})();
