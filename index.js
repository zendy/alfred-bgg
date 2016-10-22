const myHeaders = new Headers();
const myInit = { method: 'GET',
               headers : myHeaders,
               cache   : 'default' };
const searchPromise = fetch( 'https://www.boardgamegeek.com/xmlapi2/search?query=blood+rage' );

searchPromise
  .then( data => console.log(data) )
  .catch( err => console.log('here') );
