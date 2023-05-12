Nova.booting((Vue) => {
  Nova.component("DefaultField", require("./components/DefaultField").default);
  Nova.component("PanelItem", require("./components/PanelItem").default);
  Nova.component(
    "DetailHeadingField",
    require("./components/Detail/HeadingField").default
  );
  Nova.component(
    "FormHeadingField",
    require("./components/Form/HeadingField").default
  );
});
