/**
 * Triggered from a message on a Cloud Pub/Sub topic.
 *
 * @param {!Object} event Event payload.
 * @param {!Object} context Metadata for the event.
 */
const axios = require('axios').default;
exports.fetchFromRemoteBlogAPI = (event, context) => {
  return axios.get('https://api.annuched.google.com/api/v1/job/fetch-from-remote-blog-api')
    .then((response) => console.log(response))
    .catch((error) => console.log(error))
};
