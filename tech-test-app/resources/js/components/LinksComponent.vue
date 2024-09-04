<template>
    <div class="container">
      <h1>Link Harvester</h1>
      <h2>Filter Links by Tags</h2>
  
      <div class="filter-section">
        <label v-for="tag in tags" :key="tag" class="tag-label">
          <input type="checkbox" :value="tag" v-model="highlightedTags" />
          {{ tag }}
        </label>
        <button @click="getLinks" class="btn">Show Links</button>
      </div>
  
      <div v-if="links.length" class="links-section">
        <h3>Links:</h3>
        <ul>
          <li v-for="link in links" :key="link.id">
            <a :href="link.url" target="_blank">{{ link.title }}</a> - Tags: {{ link.tags.join(', ') }}
          </li>
        </ul>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        tags: ['php', 'laravel', 'vue', 'api'],
        highlightedTags: [],
        links: []
      };
    },
    methods: {
      async getLinks() {
        try {
          const response = await axios.get('/links', {
            params: { tags: this.highlightedTags.join(',') }
          });
          this.links = response.data;
        } catch (error) {
          console.error('Error fetching links:', error);
        }
      }
    }
  };
  </script>
  
  <style scoped>
  .container {
    max-width: 800px;
    margin: 20px auto;
  }
  
  h1, h2 {
    text-align: center;
  }
  
  .filter-section {
    margin-bottom: 20px;
    text-align: center;
  }
  
  .tag-label {
    margin-right: 10px;
  }
  
  .btn {
    margin-top: 10px;
  }
  
  .links-section ul {
    padding-left: 20px;
  }
  
  .links-section ul li {
    margin-bottom: 10px;
  }
  
  a {
    color: #007bff;
    text-decoration: none;
  }
  
  a:hover {
    text-decoration: underline;
  }
  </style>
  