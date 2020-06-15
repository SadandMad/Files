using System.Collections.Generic;
using System.Linq;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Mvc;
using Microsoft.AspNetCore.Hosting;
using System.IO;
using System.Text.Json;

namespace Files.Controllers
{
    [Route("")]
    [ApiController]
    public class DefaultController : ControllerBase
    {
        private readonly IWebHostEnvironment myAppEnvironment;
        public DefaultController(IWebHostEnvironment appEnvironment)
        {
            myAppEnvironment = appEnvironment;
        }

        [HttpGet()]
        [HttpGet("{dir}")]
        public IActionResult Get(string dir)
        {
            string fullPath;
            if (dir != null)
            {
                fullPath = Path.Combine(myAppEnvironment.ContentRootPath, dir);
            }
            else
            {
                fullPath = myAppEnvironment.ContentRootPath;
            }
            IReadOnlyCollection<string> dirs = Directory.GetDirectories(fullPath);
            IReadOnlyCollection<string> files = Directory.GetFiles(fullPath);
            List<string> content = new List<string>(dirs);
            content.AddRange(files);
            return new JsonResult(content, new JsonSerializerOptions { });
        }

        [HttpGet("{file}.{ext}")]
        public IActionResult Get(string file, string ext)
        {
            string filePath = Path.Combine(myAppEnvironment.ContentRootPath, file);
            filePath = filePath + '.' + ext;
            string fileType = "application/octet-stream";
            if (System.IO.File.Exists(filePath))
            {
                return PhysicalFile(filePath, fileType);
            }
            else
            {
                return NotFound();
            }
        }

        [HttpPut("{path}")]
        public IActionResult Put(IFormFile file, string path)
        {
            try
            {
                using (var fileStream = new FileStream(Path.Combine(myAppEnvironment.ContentRootPath, path), FileMode.Create))
                {
                    file.CopyTo(fileStream);
                }
                return Ok("Файл создан!");
            }
            catch
            {
                return BadRequest("Ошибка при создании файла. Извините.");
            }
        }

        [HttpHead("{file}")]
        public IActionResult Head(string file)
        {
            string filePath = Path.Combine(myAppEnvironment.ContentRootPath, file);
            if (System.IO.File.Exists(filePath))
            {
                FileInfo fileInfo = new FileInfo(filePath);
                Response.Headers.Append("Name", fileInfo.FullName);
                Response.Headers.Append("Length", fileInfo.Length.ToString());
                return Ok();
            }
            else
            {
                return (NotFound());
            }
        }

        [HttpDelete("{file}")]
        public IActionResult Delete(string file)
        {
            try
            {
                System.IO.File.Delete(Path.Combine(myAppEnvironment.ContentRootPath, file));
                return Ok("Удалено!");
            }
            catch
            {
                return NotFound();
            }
        }
    }
}