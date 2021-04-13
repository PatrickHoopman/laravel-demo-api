<template>
  <q-page class="q-pa-md">
    <q-table
      title="Products"
      :columns="columns"
      :data="sortedProducts"
      row-key="name"
    >
      <template v-slot:top-right>
        <q-input
          class="q-mr-md"
          borderless
          dense
          debounce="300"
          v-model="filter"
          placeholder="Search"
        >
          <template v-slot:append>
            <q-icon name="search" />
          </template>
        </q-input>

        <q-btn
          class="q-ml-md"
          outline
          round
          color="primary"
          icon="add"
          @click="showProductForm()"
        >
          <q-tooltip>Create new product</q-tooltip>
        </q-btn>
      </template>
      <template v-slot:body="props">
        <q-tr :props="props">
          <q-td key="serial" :props="props">
            {{ props.row.serial }}
          </q-td>
          <q-td key="name" :props="props">
            {{ props.row.name }}
          </q-td>
          <q-td key="brand" :props="props">
            {{ props.row.brand }}
          </q-td>
          <q-td key="price" :props="props"> &euro;{{ props.row.price }} </q-td>
          <q-td key="stock" :props="props">
            {{ props.row.stock }}
          </q-td>
          <q-td key="outOfStock" :props="props">
            <q-badge
              outline
              align="middle"
              :color="props.row.outOfStock ? 'warning' : 'green'"
            >
              {{ props.row.outOfStock ? "Out Of Stock" : "In Stock" }}
            </q-badge>
          </q-td>
          <q-td class="q-gutter-sm" key="actions" :props="props">
            <q-btn
              outline
              round
              color="primary"
              icon="edit"
              @click="showProductForm(props.row)"
            >
              <q-tooltip>Edit {{ props.row.name }}</q-tooltip>
            </q-btn>
            <q-btn
              outline
              round
              color="red"
              icon="delete"
              @click="removeProduct(props.row)"
            >
              <q-tooltip>Delete {{ props.row.name }}</q-tooltip>
            </q-btn>
          </q-td>
        </q-tr>
      </template>
    </q-table>
  </q-page>
</template>

<script>
import ProductForm from "../components/ProductForm.vue";
export default {
  name: "PageIndex",
  computed: {
    selectedProduct() {
      return this.products.find(
        ({ serial }) => serial === this.selectedProductSerial
      );
    },
    filteredProducts() {
      return this.products.filter((product) => {
        if (this.filter.length > 0) {
          const values = Object.values(product);
          return values.some((value) => `${value}`.includes(this.filter));
        } else {
          return true;
        }
      });
    },
    sortedProducts() {
      return this.filteredProducts.slice(0).sort((a, b) => {
        const nameA = a.name.toUpperCase();
        const nameB = b.name.toUpperCase();
        if (nameA < nameB) {
          return -1;
        }
        if (nameA > nameB) {
          return 1;
        }
        return 0;
      });
    },
  },
  data() {
    return {
      selectedProductSerial: "",
      filter: "",
      columns: [
        {
          name: "serial",
          required: true,
          label: "Serial",
          align: "left",
          field: (row) => row.serial,
          format: (val) => `${val}`,
          sortable: true,
        },
        {
          name: "name",
          required: true,
          label: "Name",
          align: "left",
          field: (row) => row.name,
          format: (val) => `${val}`,
          sortable: true,
        },
        {
          name: "brand",
          required: true,
          label: "Brand",
          align: "left",
          field: (row) => row.brand,
          format: (val) => `${val}`,
          sortable: true,
        },
        {
          name: "price",
          required: true,
          label: "Price",
          align: "left",
          field: (row) => row.price,
          format: (val) => `${val}`,
          sortable: true,
        },
        {
          name: "stock",
          required: true,
          label: "Stock",
          align: "left",
          field: (row) => row.stock,
          format: (val) => `${val}`,
          sortable: true,
        },
        {
          name: "outOfStock",
          required: true,
          label: "Out Of Stock",
          align: "left",
          field: (row) => row.outOfStock,
          format: (val) => `${val}`,
          sortable: true,
        },
        {
          name: "actions",
          required: true,
          label: "Actions",
          align: "center",
          sortable: false,
        },
      ],
      products: [],
    };
  },
  methods: {
    showProductForm(product = undefined) {
      this.$q
        .dialog({
          component: ProductForm,
          parent: this,
          product,
          isNewProduct: product === undefined,
        })
        .onOk(({ product, oldProduct, isNewProduct }) => {
          console.log("OK", { product, oldProduct });
          this.handleFormOkEvent(product, oldProduct, isNewProduct);
        })
        .onCancel(() => {
          console.log("Cancel");
        })
        .onDismiss(() => {
          console.log("Called on OK or Cancel");
        });
    },
    handleFormOkEvent(product, oldProduct, createNew) {
      if (createNew) {
        this.storeProduct(product);
      } else {
        this.updateProduct(oldProduct.serial, product, oldProduct);
      }
    },
    fetchProducts() {
      this.$api
        .get("/products")
        .then(({ data: { products } }) => {
          this.products = products;
        })
        .catch((error) => {
          console.error(error);
        });
    },
    removeProduct({ serial }) {
      this.$api.delete(`/products/${serial}`).then(({ data: { product } }) => {
        this.products = this.products.filter(
          ({ serial }) => serial !== product.serial
        );
        this.notifyDeleteSuccess(serial);
      });
    },
    storeProduct(product) {
      return this.$api
        .post(`/products`, product)
        .then(() => {
          this.fetchProducts();
        })
        .catch((error) => {
          console.error(error);
        });
    },
    updateProduct(serial, newProduct, oldProduct = undefined) {
      return this.$api
        .patch(`/products/${serial}`, newProduct)
        .then(() => {
          if (oldProduct) {
            this.notifyUpdateSuccess(serial, oldProduct);
            this.products = this.products.map((product) => {
              if (serial === product.serial) {
                return newProduct;
              } else {
                return product;
              }
            });
          } else {
            this.fetchProducts();
          }
        })
        .catch((error) => {
          console.error(error);
        });
    },
    notifyDeleteSuccess(serial) {
      this.$q.notify({
        message: `Successfully deleted product with serial ${serial}`,
        color: "warning",
        actions: [
          {
            label: "Undo",
            color: "white",
            handler: () => {
              this.updateProduct(serial, { restore: true })
                .then(() => {
                  this.notifyRestoreDeleteSuccess(serial);
                })
                .catch((error) => {
                  console.error(error);
                });
            },
          },
        ],
      });
    },
    notifyRestoreDeleteSuccess(serial) {
      this.$q.notify({
        color: "green",
        message: `Successfully restored deletion of ${serial}`,
      });
    },
    notifyUpdateSuccess(serial, oldProduct) {
      this.$q.notify({
        message: `Successfully updated product ${serial}`,
        color: "green",
        actions: [
          {
            label: "Undo",
            color: "white",
            handler: () => {
              this.updateProduct(serial, oldProduct)
                .then(() => {
                  this.notifyRestoreUpdateSuccess(attributeName, oldProduct);
                })
                .catch((error) => {
                  console.error(error);
                });
            },
          },
        ],
      });
    },
    notifyRestoreUpdateSuccess(attributeName, oldValue) {
      this.$q.notify({
        color: "green",
        message: `Successfully restored ${attributeName} to ${oldValue}`,
      });
    },
  },
  mounted() {
    this.fetchProducts();
  },
};
</script>
