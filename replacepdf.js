const fs = require('fs');

const dirPath = 'public/storage/anexos';
const files = fs.readdirSync(dirPath);

files.forEach(file => {
    const fileExtension = file.split('.').pop();
    const extensionsValidate = [
        'png',
        'jpg',
        'jpeg',
        'mp4',
        'pdf',
        'docx',
    ]
    if (!extensionsValidate.includes(fileExtension)) {
        const newFile = file + '.pdf';
        fs.renameSync(`${dirPath}/${file}`, `${dirPath}/${newFile}`);
        console.log(`${file} -> ${newFile}`);
    }
});
