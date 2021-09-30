(function() {
  "use strict";
  var render$a = function() {
    var _vm = this;
    var _h = _vm.$createElement;
    var _c = _vm._self._c || _h;
    return _c("ul", { staticClass: "lbvs-changes" }, _vm._l(_vm.changes, function(status, path) {
      return _c("li", { key: path }, [_c("span", { attrs: { "data-status": status, "title": _vm.$t("versions.label.status." + status) } }, [_vm._v(" " + _vm._s(status) + " ")]), _vm._v(" " + _vm._s(path) + " ")]);
    }), 0);
  };
  var staticRenderFns$a = [];
  var Changes_vue_vue_type_style_index_0_lang = '\n.lbvs-changes {\n  font-family: var(--font-family-mono);\n}\n.lbvs-changes li {\n  padding-left: 1.2em;\n  position: relative;\n}\n.lbvs-changes li span {\n  position: absolute;\n  left: 0;\n\n  font-weight: bold;\n}\n.lbvs-changes li span[data-status="+"],\n.lbvs-changes li span[data-status="C"] {\n  color: var(--color-positive);\n}\n.lbvs-changes li span[data-status="-"] {\n  color: var(--color-negative);\n}\n.lbvs-changes li span[data-status="M"],\n.lbvs-changes li span[data-status="R"] {\n  color: var(--color-notice);\n}\n';
  function normalizeComponent(scriptExports, render2, staticRenderFns2, functionalTemplate, injectStyles2, scopeId, moduleIdentifier, shadowMode) {
    var options = typeof scriptExports === "function" ? scriptExports.options : scriptExports;
    if (render2) {
      options.render = render2;
      options.staticRenderFns = staticRenderFns2;
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
        context = context || this.$vnode && this.$vnode.ssrContext || this.parent && this.parent.$vnode && this.parent.$vnode.ssrContext;
        if (!context && typeof __VUE_SSR_CONTEXT__ !== "undefined") {
          context = __VUE_SSR_CONTEXT__;
        }
        if (injectStyles2) {
          injectStyles2.call(this, context);
        }
        if (context && context._registeredComponents) {
          context._registeredComponents.add(moduleIdentifier);
        }
      };
      options._ssrRegister = hook;
    } else if (injectStyles2) {
      hook = shadowMode ? function() {
        injectStyles2.call(this, (options.functional ? this.parent : this).$root.$options.shadowRoot);
      } : injectStyles2;
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
  const script$a = {
    props: {
      changes: Object
    }
  };
  const __cssModules$a = {};
  var __component__$a = /* @__PURE__ */ normalizeComponent(script$a, render$a, staticRenderFns$a, false, injectStyles$a, null, null, null);
  function injectStyles$a(context) {
    for (let o in __cssModules$a) {
      this[o] = __cssModules$a[o];
    }
  }
  var Changes = /* @__PURE__ */ function() {
    return __component__$a.exports;
  }();
  var render$9 = function() {
    var _vm = this;
    var _h = _vm.$createElement;
    var _c = _vm._self._c || _h;
    return _c("k-dialog", { ref: "dialog", staticClass: "lbvs-create-error-dialog", attrs: { "cancel-button": _vm.$t("close"), "submit-button": false } }, [_c("p", { staticClass: "lbvs-create-error-dialog-message" }, [_vm._v(" " + _vm._s(_vm.error.message) + " ")]), _c("ul", { staticClass: "lbvs-create-error-dialog-list" }, _vm._l(_vm.error.details.lockedModels, function(users, model) {
      return _c("li", { key: model }, [_vm._v(" " + _vm._s(model) + " "), _c("span", [_vm._v("(" + _vm._s(users.join(", ")) + ")")])]);
    }), 0)]);
  };
  var staticRenderFns$9 = [];
  var CreateErrorDialog_vue_vue_type_style_index_0_lang = "\n.lbvs-create-error-dialog {\n  line-height: 1.5;\n}\n.lbvs-create-error-dialog-message {\n  margin-bottom: 1rem;\n\n  color: var(--color-negative);\n}\n.lbvs-create-error-dialog-list li {\n  margin-left: 1.2rem;\n\n  list-style: disc;\n}\n.lbvs-create-error-dialog-list span {\n  color: var(--color-text-light);\n}\n";
  const script$9 = {
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
  const __cssModules$9 = {};
  var __component__$9 = /* @__PURE__ */ normalizeComponent(script$9, render$9, staticRenderFns$9, false, injectStyles$9, null, null, null);
  function injectStyles$9(context) {
    for (let o in __cssModules$9) {
      this[o] = __cssModules$9[o];
    }
  }
  var CreateErrorDialog = /* @__PURE__ */ function() {
    return __component__$9.exports;
  }();
  var render$8 = function() {
    var _vm = this;
    var _h = _vm.$createElement;
    var _c = _vm._self._c || _h;
    return _c("div", [_c("k-dialog", { ref: "dialog", attrs: { "size": "large", "submit-button": _vm.$t("versions.button.create"), "theme": "positive" }, on: { "submit": _vm.onSubmit } }, [_c("k-form", { ref: "form", attrs: { "fields": _vm.fields }, on: { "submit": _vm.onSubmit }, scopedSlots: _vm._u([{ key: "header", fn: function() {
      return [_c("k-field", { staticClass: "lbvs-create-changes", attrs: { "label": _vm.$t("versions.label.changes") } }, [_c("lbvs-changes", { attrs: { "changes": _vm.stagedChanges } })], 1)];
    }, proxy: true }]) })], 1), _c("lbvs-create-error-dialog", { ref: "errorDialog" })], 1);
  };
  var staticRenderFns$8 = [];
  var CreateDialog_vue_vue_type_style_index_0_lang = "\n.lbvs-create-changes {\n  margin-bottom: 2.25rem;\n}\n";
  const script$8 = {
    data() {
      return {
        instance: null,
        inProgress: false,
        stagedChanges: {}
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
      async open(instance) {
        this.instance = instance;
        try {
          this.stagedChanges = await this.$store.dispatch({
            type: "versions/prepareVersionCreation",
            instance: this.instance
          });
        } catch (e) {
          if (e.key === "error.versions.lockFiles") {
            return this.$refs.errorDialog.open(e);
          }
          throw e;
        }
        this.$refs.dialog.open();
      }
    }
  };
  const __cssModules$8 = {};
  var __component__$8 = /* @__PURE__ */ normalizeComponent(script$8, render$8, staticRenderFns$8, false, injectStyles$8, null, null, null);
  function injectStyles$8(context) {
    for (let o in __cssModules$8) {
      this[o] = __cssModules$8[o];
    }
  }
  var CreateDialog = /* @__PURE__ */ function() {
    return __component__$8.exports;
  }();
  var render$7 = function() {
    var _vm = this;
    var _h = _vm.$createElement;
    var _c = _vm._self._c || _h;
    return _c("k-dialog", { ref: "dialog", staticClass: "lbvs-version-dialog lbvs-version-delete-dialog", attrs: { "icon": "trash", "submit-button": _vm.$t("versions.button.delete"), "theme": "negative" }, on: { "submit": _vm.onSubmit } }, [_vm.version ? _c("lbvs-version", { attrs: { "version": _vm.version } }) : _vm._e(), _c("p", [_vm._v(_vm._s(_vm.$t("versions.message.delete")))])], 1);
  };
  var staticRenderFns$7 = [];
  var DeleteDialog_vue_vue_type_style_index_0_lang = "\n.lbvs-version-delete-dialog {\n  line-height: 1.5;\n}\n";
  const script$7 = {
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
  const __cssModules$7 = {};
  var __component__$7 = /* @__PURE__ */ normalizeComponent(script$7, render$7, staticRenderFns$7, false, injectStyles$7, null, null, null);
  function injectStyles$7(context) {
    for (let o in __cssModules$7) {
      this[o] = __cssModules$7[o];
    }
  }
  var DeleteDialog = /* @__PURE__ */ function() {
    return __component__$7.exports;
  }();
  var render$6 = function() {
    var _vm = this;
    var _h = _vm.$createElement;
    var _c = _vm._self._c || _h;
    return _c("k-dialog", { ref: "dialog", staticClass: "lbvs-version-dialog", attrs: { "submit-button": _vm.$t("versions.button.deploy"), "theme": "positive" }, on: { "submit": _vm.onSubmit } }, [_vm.version ? _c("lbvs-version", { attrs: { "version": _vm.version } }) : _vm._e(), _c("k-form", { ref: "form", attrs: { "fields": _vm.fields }, on: { "submit": _vm.onSubmit } })], 1);
  };
  var staticRenderFns$6 = [];
  const script$6 = {
    data() {
      return {
        inProgress: false,
        version: null
      };
    },
    computed: {
      fields() {
        let currentInstance = this.$store.getters["versions/currentInstance"];
        let options = Object.values(this.$store.state.versions.data.instances).map((instance) => ({ text: instance.name, value: instance.name }));
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
  const __cssModules$6 = {};
  var __component__$6 = /* @__PURE__ */ normalizeComponent(script$6, render$6, staticRenderFns$6, false, injectStyles$6, null, null, null);
  function injectStyles$6(context) {
    for (let o in __cssModules$6) {
      this[o] = __cssModules$6[o];
    }
  }
  var DeployDialog = /* @__PURE__ */ function() {
    return __component__$6.exports;
  }();
  var render$5 = function() {
    var _vm = this;
    var _h = _vm.$createElement;
    var _c = _vm._self._c || _h;
    return _c("k-dialog", { ref: "dialog", staticClass: "lbvs-version-dialog", attrs: { "cancel-button": _vm.$t(_vm.data ? "close" : "cancel"), "submit-button": false } }, [_vm.version ? _c("lbvs-version", { attrs: { "details": _vm.details, "version": _vm.version } }) : _vm._e(), !_vm.data ? _c("p", [_vm._v(" " + _vm._s(_vm.$t("versions.message.exporting")) + " ")]) : _c("k-button-group", [_c("k-button", { attrs: { "icon": "download" }, on: { "click": _vm.download } }, [_vm._v(" " + _vm._s(_vm.$t("versions.button.download")) + " ")]), _c("k-button", { attrs: { "icon": "copy", "disabled": !_vm.supportsClipboard }, on: { "click": _vm.copyToClipboard } }, [_vm._v(" " + _vm._s(_vm.$t("versions.button.copyLink")) + " ")])], 1)], 1);
  };
  var staticRenderFns$5 = [];
  const script$5 = {
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
  const __cssModules$5 = {};
  var __component__$5 = /* @__PURE__ */ normalizeComponent(script$5, render$5, staticRenderFns$5, false, injectStyles$5, null, null, null);
  function injectStyles$5(context) {
    for (let o in __cssModules$5) {
      this[o] = __cssModules$5[o];
    }
  }
  var ExportDialog = /* @__PURE__ */ function() {
    return __component__$5.exports;
  }();
  var render$4 = function() {
    var _vm = this;
    var _h = _vm.$createElement;
    var _c = _vm._self._c || _h;
    return _c(_vm.element, { tag: "component", staticClass: "lbvs-instance-name", style: { backgroundColor: _vm.instance.color } }, [_vm._v(" " + _vm._s(_vm.instance.name) + " ")]);
  };
  var staticRenderFns$4 = [];
  var InstanceName_vue_vue_type_style_index_0_lang = "\n.lbvs-instance-name {\n  display: inline-block;\n  padding: 0 0.3em;\n\n  border-radius: 3px;\n  color: var(--color-text);\n}\nstrong.lbvs-instance-name {\n  padding: 0.2em 0.4em;\n}\n";
  const script$4 = {
    props: {
      inline: Boolean,
      instance: Object
    },
    computed: {
      element() {
        return this.inline ? "span" : "strong";
      }
    }
  };
  const __cssModules$4 = {};
  var __component__$4 = /* @__PURE__ */ normalizeComponent(script$4, render$4, staticRenderFns$4, false, injectStyles$4, null, null, null);
  function injectStyles$4(context) {
    for (let o in __cssModules$4) {
      this[o] = __cssModules$4[o];
    }
  }
  var InstanceName = /* @__PURE__ */ function() {
    return __component__$4.exports;
  }();
  var render$3 = function() {
    var _vm = this;
    var _h = _vm.$createElement;
    var _c = _vm._self._c || _h;
    return _c("div", { staticClass: "lbvs-status" }, [_c("k-view", [_c("k-grid", [_c("k-column", { attrs: { "width": "1/3" } }, [_c("header", { staticClass: "k-section-header" }, [_c("k-headline", [_vm._v(" " + _vm._s(_vm.$t("versions.label.instances")) + " ")])], 1), _c("ul", { staticClass: "lbvs-status-instances" }, _vm._l(_vm.$store.state.versions.data.instances, function(instance) {
      return _c("li", { key: instance.name, class: { current: instance.isCurrent } }, [_c("lbvs-instance-name", { attrs: { "instance": instance } }), instance.isCurrent ? _c("span", { staticClass: "lbvs-status-current" }, [_vm._v(" " + _vm._s(_vm.$t("versions.label.current")) + " ")]) : _vm._e(), instance.version ? _c("lbvs-version", { attrs: { "version": { name: instance.version, label: instance.versionLabel } } }) : _vm._e()], 1);
    }), 0)]), _c("k-column", { staticClass: "lbvs-status-changes", attrs: { "width": "2/3" } }, [_c("header", { staticClass: "k-section-header" }, [_c("k-headline", [_vm._v(" " + _vm._s(_vm.$t("versions.label.changes")) + " ")]), _c("k-button-group", [_c("k-button", { attrs: { "icon": "add", "disabled": _vm.canCreateVersion === false }, on: { "click": _vm.onCreate } }, [_vm._v(" " + _vm._s(_vm.$t("versions.button.create")) + " ")])], 1)], 1), _c("lbvs-changes", { attrs: { "changes": _vm.currentChanges } })], 1)], 1)], 1), _c("lbvs-create-dialog", { ref: "createDialog" })], 1);
  };
  var staticRenderFns$3 = [];
  var Status_vue_vue_type_style_index_0_lang = "\n.lbvs-status {\n  padding-top: 1.5rem;\n  padding-bottom: 2rem;\n\n  background: #2b2b2b;\n  color: #fff;\n}\n.lbvs-status .k-grid {\n  /* gap between the columns on mobile */\n  grid-row-gap: 1.5rem;\n}\n.lbvs-status-instances li {\n  padding: 0.8rem;\n}\n.lbvs-status-instances li.current {\n  background: var(--color-background);\n  color: var(--color-text);\n}\n.lbvs-status-current {\n  margin-left: 0.5rem;\n  padding: 0.1em 0.3em;\n\n  border: 1px solid var(--color-border);\n  border-radius: 3px;\n\n  font-size: var(--font-size-small);\n}\n.lbvs-status-instances .lbvs-version {\n  margin-top: 0.4rem;\n}\n.lbvs-status-changes > div {\n  /* ensure the list of changes fills the whole view vertically */\n  display: flex;\n  flex-direction: column;\n  height: 100%;\n}\n.lbvs-status-changes .lbvs-changes {\n  height: 100%;\n  padding: 0.8rem;\n\n  background: var(--color-background);\n  color: var(--color-text);\n}\n";
  const script$3 = {
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
        return this.$refs.createDialog.open(instance);
      }
    }
  };
  const __cssModules$3 = {};
  var __component__$3 = /* @__PURE__ */ normalizeComponent(script$3, render$3, staticRenderFns$3, false, injectStyles$3, null, null, null);
  function injectStyles$3(context) {
    for (let o in __cssModules$3) {
      this[o] = __cssModules$3[o];
    }
  }
  var Status = /* @__PURE__ */ function() {
    return __component__$3.exports;
  }();
  var render$2 = function() {
    var _vm = this;
    var _h = _vm.$createElement;
    var _c = _vm._self._c || _h;
    return _c("div", { staticClass: "lbvs-version" }, [_c("span", { staticClass: "lbvs-version-header" }, [_c("strong", [_vm._v(_vm._s(_vm.version.label))]), _vm.instances ? _vm._l(_vm.version.instances, function(instance) {
      return _c("lbvs-instance-name", { key: instance, attrs: { "inline": true, "instance": _vm.$store.state.versions.data.instances[instance] } });
    }) : _vm._e()], 2), _c("dl", { staticClass: "lbvs-version-details" }, [_vm._l(_vm.mergedDetails, function(detail) {
      return [_c("dt", { key: detail.title, staticClass: "k-offscreen" }, [_vm._v(" " + _vm._s(detail.title) + ": ")]), _c("dd", { key: detail.title, attrs: { "title": detail.title } }, [_vm._v(" " + _vm._s(detail.value) + " ")])];
    })], 2)]);
  };
  var staticRenderFns$2 = [];
  var Version_vue_vue_type_style_index_0_lang = '\n.lbvs-version {\n  line-height: 1.4;\n}\n.lbvs-version-header {\n  display: block;\n}\n.lbvs-version .lbvs-instance-name {\n  margin-right: 0.3em;\n}\n.lbvs-version-details {\n  font-size: var(--font-size-small);\n}\n.lbvs-version-details dd {\n  display: inline;\n}\n.lbvs-version-details dd:not(:last-child)::after {\n  content: " \xB7 ";\n\n  color: var(--color-text-light);\n}\n';
  const script$2 = {
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
  const __cssModules$2 = {};
  var __component__$2 = /* @__PURE__ */ normalizeComponent(script$2, render$2, staticRenderFns$2, false, injectStyles$2, null, null, null);
  function injectStyles$2(context) {
    for (let o in __cssModules$2) {
      this[o] = __cssModules$2[o];
    }
  }
  var Version = /* @__PURE__ */ function() {
    return __component__$2.exports;
  }();
  var render$1 = function() {
    var _vm = this;
    var _h = _vm.$createElement;
    var _c = _vm._self._c || _h;
    return _c("div", { staticClass: "lbvs-versions" }, [_c("k-view", [_c("header", { staticClass: "k-section-header" }, [_c("k-headline", [_vm._v(" " + _vm._s(_vm.$t("versions.label.versions")) + " ")])], 1), _vm.items.length ? _c("k-list", _vm._l(_vm.items, function(item, index) {
      return _c("k-list-item", _vm._b({ key: index, attrs: { "image": true, "options": _vm.options(item) }, on: { "action": function($event) {
        return _vm.onOption($event, item);
      } }, scopedSlots: _vm._u([{ key: "image", fn: function() {
        return [_vm._v(" " + _vm._s(index + 1) + " ")];
      }, proxy: true }], null, true) }, "k-list-item", item, false), [_c("lbvs-version", { attrs: { "details": _vm.versionDetails(item), "instances": true, "version": item } })], 1);
    }), 1) : _c("k-empty", { attrs: { "layout": "cards" } }, [_vm._v(" " + _vm._s(_vm.$t("versions.label.empty")) + " ")])], 1), _c("lbvs-export-dialog", { ref: "exportDialog" }), _c("lbvs-deploy-dialog", { ref: "deployDialog" }), _c("lbvs-delete-dialog", { ref: "deleteDialog" })], 1);
  };
  var staticRenderFns$1 = [];
  var Versions_vue_vue_type_style_index_0_lang = "\n.lbvs-versions {\n  padding-top: 1.5rem;\n}\n.lbvs-versions .k-list-item-image {\n  font-size: var(--font-size-small);\n  line-height: 38px;\n  text-align: center;\n\n  color: var(--color-text-light);\n}\n.lbvs-versions .k-list-item-text {\n  white-space: initial;\n}\n";
  const script$1 = {
    computed: {
      items() {
        return Object.values(this.$store.state.versions.data.versions);
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
      },
      versionDetails(version) {
        return [
          {
            title: this.$t("versions.label.creation"),
            value: this.versionDetailsToString("creationData", {
              created: version.created ? this.$library.dayjs.unix(version.created).format("YYYY-MM-DD HH:mm") : "?",
              creator: version.creatorName || version.creatorEmail || "?"
            })
          },
          {
            title: this.$t("versions.label.originInstance"),
            value: this.versionDetailsToString("from", {
              originInstance: version.originInstance || "?"
            })
          }
        ];
      },
      versionDetailsToString(key, data) {
        if (Object.values(data).every((value) => value === "?") === true) {
          return null;
        }
        return this.$t("versions.label." + key, data);
      }
    }
  };
  const __cssModules$1 = {};
  var __component__$1 = /* @__PURE__ */ normalizeComponent(script$1, render$1, staticRenderFns$1, false, injectStyles$1, null, null, null);
  function injectStyles$1(context) {
    for (let o in __cssModules$1) {
      this[o] = __cssModules$1[o];
    }
  }
  var Versions = /* @__PURE__ */ function() {
    return __component__$1.exports;
  }();
  var render = function() {
    var _vm = this;
    var _h = _vm.$createElement;
    var _c = _vm._self._c || _h;
    return _c("k-inside", [_c("div", { staticClass: "lbvs-view" }, [_vm.isLoading ? _c("k-loader") : [_c("lbvs-status"), _c("lbvs-versions")]], 2)]);
  };
  var staticRenderFns = [];
  var View_vue_vue_type_style_index_0_lang = ".lbvs-version-dialog .lbvs-version {\n    margin-bottom: 1.5rem;\n}\n.lbvs-view > .k-loader {\n  position: absolute;\n  top: 50%;\n  left: 50%;\n  transform: translate(-50%, -50%);\n}\n";
  const script = {
    data() {
      return {
        isLoading: true
      };
    },
    async mounted() {
      try {
        this.isLoading = true;
        await this.$store.dispatch("versions/load");
      } finally {
        this.isLoading = false;
      }
    }
  };
  const __cssModules = {};
  var __component__ = /* @__PURE__ */ normalizeComponent(script, render, staticRenderFns, false, injectStyles, null, null, null);
  function injectStyles(context) {
    for (let o in __cssModules) {
      this[o] = __cssModules[o];
    }
  }
  var View = /* @__PURE__ */ function() {
    return __component__.exports;
  }();
  var Store = (app) => ({
    namespaced: true,
    state: {
      data: {
        instances: {},
        versions: {}
      }
    },
    getters: {
      currentInstance(state) {
        return Object.values(state.data.instances).find((instance) => instance.isCurrent);
      }
    },
    mutations: {
      SET_DATA(state, { instances, versions }) {
        state.data.instances = instances;
        state.data.versions = versions;
      }
    },
    actions: {
      async load({ commit }) {
        commit("SET_DATA", await app.$api.get("versions"));
      },
      async prepareVersionCreation(context, { instance }) {
        return await app.$api.post("versions/prepareCreation", { instance });
      },
      async createVersion({ commit }, { instance, label }) {
        let data = await app.$api.post("versions/create", { instance, label });
        commit("SET_DATA", data);
      },
      async deleteVersion({ commit }, { version }) {
        let data = await app.$api.delete("versions/versions/" + version);
        commit("SET_DATA", data);
      },
      async deployVersion({ commit }, { instance, version }) {
        let data = await app.$api.post("versions/versions/" + version + "/deploy", { instance });
        commit("SET_DATA", data);
      },
      async exportVersion(context, { version }) {
        return await app.$api.post("versions/versions/" + version + "/export");
      }
    }
  });
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
      "lbvs-versions": Versions,
      "lbvs-versions-view": View
    },
    created(app) {
      app.$store.registerModule("versions", Store(app));
    }
  });
})();
