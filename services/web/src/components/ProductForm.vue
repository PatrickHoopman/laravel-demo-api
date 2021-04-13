<template>
  <q-dialog ref="dialog" @hide="onDialogHide">
    <q-card style="width: 400px" class="q-pa-md">
      <q-card-section>
        <div class="row items-center no-wrap">
          <div class="col">
            <div class="text-h6" v-text="title" />
          </div>

          <div class="col-auto">
            <q-btn color="grey-7" round flat icon="close" @click="hide" />
          </div>
        </div>
      </q-card-section>
      <div class="q-gutter-md">
        <q-input outlined v-model="serial" label="Serial" />
        <q-input outlined v-model="name" label="Name" />
        <q-input outlined v-model="brand" label="Brand" />
        <q-input outlined v-model="stock" type="number" label="Stock" />
        <q-input outlined v-model="price" prefix="€" label="Price" />
      </div>

      <q-card-actions align="right">
        <q-btn flat label="Save" color="primary" @click="onOkClick" />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script>
export default {
  props: {
    isNewProduct: {
      type: Boolean,
      default: false,
    },
    product: {
      type: Object,
      default: function () {
        return {
          serial: "",
          name: "",
          brand: "",
          stock: 0,
          price: 0.0,
        };
      },
    },
  },
  computed: {
    title() {
      return `${this.isNewProduct ? "Create" : "Edit"} Product`;
    },
    newProduct() {
      return {
        serial: this.serial,
        name: this.name,
        brand: this.brand,
        stock: this.stock,
        price: this.price,
      };
    },
  },
  data() {
    return {
      oldProduct: undefined,
      serial: "",
      name: "",
      brand: "",
      stock: 0,
      price: 0.0,
    };
  },
  methods: {
    show() {
      this.$refs.dialog.show();
    },
    hide() {
      this.$refs.dialog.hide();
    },
    onDialogHide() {
      this.$emit("hide");
    },
    onOkClick() {
      this.$emit("ok", {
        product: this.newProduct,
        oldProduct: { ...this.oldProduct },
        isNewProduct: this.isNewProduct,
      });
      this.hide();
    },
  },
  mounted() {
    this.oldProduct = this.product;
    this.serial = this.product.serial;
    this.name = this.product.name;
    this.brand = this.product.brand;
    this.stock = this.product.stock;
    this.price = this.product.price;
  },
};
</script>