// const myHeaders = new Headers();
// const myInit = { method: 'GET',
//                headers : myHeaders,
//                cache   : 'default' };
// const searchPromise = fetch( 'https://www.boardgamegeek.com/xmlapi2/search?query=blood+rage' );
//
// searchPromise
//   .then( data => console.log(data) )
//   .catch( err => console.log('here') );

const processStatus = function (response) {
    // status "0" to handle local files fetching (e.g. Cordova/Phonegap etc.)
  if (response.status === 200 || response.status === 0) {
    return Promise.resolve(response);
  } else {
    return Promise.reject(new Error(response.statusText));
  }
};

fetch('https://www.boardgamegeek.com/xmlapi2/search?query=blood+rage', { mode: 'cors' })
    .then(processStatus)
    // the following code added for example only
    .then(data => console.log(data))
    .catch();
