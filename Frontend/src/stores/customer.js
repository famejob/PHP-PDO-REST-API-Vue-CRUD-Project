import { defineStore } from "pinia";
import axios from "axios";
const base_url = "http://localhost/php_crud+vue/Backend/api/rest_api.php";
export const useCustomerStore = defineStore("customer", {
  state: () => ({
    customers: [],
    customer: {},
  }),
  actions: {
    async showCustomerList() {
      try {
        const response = await axios.get(`${base_url}?endpoint=customers`);
        this.customers = response.data;
      } catch (error) {
        return error;
      }
    },
    async addCustomer(Datacustomer) {
      const data_save = {
        first_name: Datacustomer.first_name,
        last_name: Datacustomer.last_name,
        email: Datacustomer.email,
        phone: Datacustomer.phone,
      };
      try {
        const response = await axios.post(
          `${base_url}?endpoint=customers`,
          data_save
        );
      } catch (error) {
        return error;
      }
    },
    async showDetailCustomer(customerID) {
      try {
        const response = await axios.get(
          `${base_url}?endpoint=customer&id=${customerID}`
        );
        this.customer = response.data;
      } catch (error) {
        return error;
      }
    },
    async editCustomer(Datacustomer, customerID) {
      const data_save = {
        first_name: Datacustomer.first_name,
        last_name: Datacustomer.last_name,
        email: Datacustomer.email,
        phone: Datacustomer.phone,
      };
      try {
        const response = await axios.put(
          `${base_url}?endpoint=customer&id=${customerID}`,
          data_save
        );
      } catch (error) {
        return error;
      }
    },
    async deleteCustomer(customerID) {
      try {
        const response = await axios.delete(
          `${base_url}?endpoint=customer&id=${customerID}`
        );
      } catch (error) {
        return error;
      }
    },
  },
});
